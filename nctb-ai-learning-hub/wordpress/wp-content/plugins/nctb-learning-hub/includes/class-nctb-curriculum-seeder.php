<?php
/**
 * One-time sample curriculum & gold-standard lesson seeder.
 *
 * Creates a single prototype tree (one SSC English book → one unit → one
 * lesson, with concepts, learning outcomes, and the complete 14-activity
 * gold-standard lesson experience) so the entire platform flow can be
 * demonstrated. Runs once, guarded by option flags, and never overwrites
 * custom user-authored content.
 *
 * Per the plan (Phase 4): enter only enough data for one gold-standard prototype lesson.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Curriculum_Seeder
 */
class NCTB_Curriculum_Seeder {

	const SEEDED_OPTION            = 'nctb_lh_sample_seeded';
	const ACTIVITIES_SEEDED_OPTION = 'nctb_lh_sample_activities_seeded';
	const QUESTIONS_SEEDED_OPTION  = 'nctb_lh_sample_questions_seeded';

	/**
	 * Seed the sample tree, activities, and practice questions once.
	 *
	 * @return void
	 */
	public static function maybe_seed() {
		$lesson_id = 0;

		if ( ! get_option( self::SEEDED_OPTION ) ) {
			// Do not seed if an editor already created books.
			$existing = get_posts(
				array(
					'post_type'   => NCTB_Curriculum_CPT::CPT_BOOK,
					'post_status' => 'any',
					'numberposts' => 1,
					'fields'      => 'ids',
				)
			);
			if ( ! empty( $existing ) ) {
				update_option( self::SEEDED_OPTION, 1, false );
			} else {
				// Book.
				$book_id = wp_insert_post(
					array(
						'post_type'    => NCTB_Curriculum_CPT::CPT_BOOK,
						'post_status'  => 'publish',
						'post_title'   => 'English For Today — SSC (Sample)',
						'post_content' => 'Sample NCTB SSC English book used to demonstrate the browse experience. Replace with real curriculum content.',
						'menu_order'   => 1,
					)
				);
				if ( $book_id && ! is_wp_error( $book_id ) ) {
					wp_set_object_terms( $book_id, 'SSC', 'nctb_class_level' );
					wp_set_object_terms( $book_id, 'English 1st Paper', 'nctb_subject' );

					// Unit.
					$unit_id = wp_insert_post(
						array(
							'post_type'    => NCTB_Curriculum_CPT::CPT_UNIT,
							'post_status'  => 'publish',
							'post_title'   => 'Unit 1 — People and Relationships (Sample)',
							'post_content' => 'Sample unit.',
							'menu_order'   => 1,
						)
					);
					update_post_meta( $unit_id, NCTB_Curriculum_CPT::META_BOOK_ID, $book_id );

					// Lesson.
					$lesson_id = wp_insert_post(
						array(
							'post_type'    => NCTB_Curriculum_CPT::CPT_LESSON,
							'post_status'  => 'publish',
							'post_title'   => 'Lesson 1 — Nelson Mandela, from Prisoner to President (Sample)',
							'post_content' => "This is a gold-standard interactive prototype lesson covering the historical biography of Nelson Mandela, contextual vocabulary, narrative grammar, interactive practice, writing, listening, speaking, quiz placeholder, and contextual AI tutor.",
							'menu_order'   => 1,
						)
					);
					update_post_meta( $lesson_id, NCTB_Curriculum_CPT::META_UNIT_ID, $unit_id );
					wp_set_object_terms( $lesson_id, 'Reading', 'nctb_topic' );

					// Concepts.
					$c1 = NCTB_Curriculum_Data::create_concept(
						array(
							'name'        => 'Main idea of a text',
							'subject'     => 'english_1st',
							'description' => 'Identifying the central point of a paragraph or passage.',
						)
					);
					$c2 = NCTB_Curriculum_Data::create_concept(
						array(
							'name'        => 'Context clues for vocabulary',
							'subject'     => 'english_1st',
							'description' => 'Guessing word meaning from surrounding text.',
						)
					);

					$concept_ids = array();
					if ( is_int( $c1 ) ) {
						$concept_ids[] = $c1;
					}
					if ( is_int( $c2 ) ) {
						$concept_ids[] = $c2;
					}
					if ( $concept_ids ) {
						NCTB_Curriculum_Data::set_lesson_concepts( $lesson_id, $concept_ids );
					}

					// Learning outcomes.
					NCTB_Curriculum_Data::set_lesson_outcomes(
						$lesson_id,
						array(
							'Identify the main idea and key historical milestones in the Nelson Mandela passage.',
							'Master 6 academic vocabulary words (apartheid, emancipation, reconciliation, icon, discrimination, resilience).',
							'Apply past narrative tenses and relative clauses in written responses.',
							'Engage in guided comprehension practice with self-evaluating hints.',
						)
					);

					update_option( self::SEEDED_OPTION, 1, false );
					NCTB_Logger::info( 'Seeded sample curriculum tree', array( 'book' => $book_id, 'unit' => $unit_id, 'lesson' => $lesson_id ) );
				}
			}
		}

		// Phase 4: Seed sample activities for the prototype lesson if not yet seeded.
		self::maybe_seed_activities( $lesson_id );

		// Phase 5: Seed sample practice questions for the prototype lesson if not yet seeded.
		self::maybe_seed_questions( $lesson_id );
	}

	/**
	 * Seed the 14 gold-standard activity blocks for the prototype lesson.
	 *
	 * @param int $target_lesson_id Optional target lesson ID.
	 * @return void
	 */
	public static function maybe_seed_activities( $target_lesson_id = 0 ) {
		if ( get_option( self::ACTIVITIES_SEEDED_OPTION ) ) {
			return;
		}

		if ( ! $target_lesson_id ) {
			$lessons = get_posts(
				array(
					'post_type'   => NCTB_Curriculum_CPT::CPT_LESSON,
					'post_status' => 'publish',
					'numberposts' => 1,
					'orderby'     => 'menu_order',
					'order'       => 'ASC',
				)
			);
			if ( ! empty( $lessons ) ) {
				$target_lesson_id = $lessons[0]->ID;
			}
		}

		if ( ! $target_lesson_id ) {
			return;
		}

		// Check if activities already exist for this lesson.
		$existing_acts = NCTB_Curriculum_Data::get_lesson_activities( $target_lesson_id, false );
		if ( ! empty( $existing_acts ) ) {
			update_option( self::ACTIVITIES_SEEDED_OPTION, 1, false );
			return;
		}

		$gold_standard_activities = self::get_gold_standard_sample_activities();
		NCTB_Curriculum_Data::set_lesson_activities( $target_lesson_id, $gold_standard_activities );

		update_option( self::ACTIVITIES_SEEDED_OPTION, 1, false );
		NCTB_Logger::info( 'Seeded gold-standard lesson activity blocks', array( 'lesson_id' => $target_lesson_id, 'count' => count( $gold_standard_activities ) ) );
	}

	/**
	 * Return the definition of all 14 gold-standard activity blocks for the sample lesson.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	public static function get_gold_standard_sample_activities() {
		return array(
			// 1. Objective
			array(
				'activity_type' => NCTB_Lesson_Activity_Types::TYPE_OBJECTIVE,
				'title'         => 'শিখন উদ্দেশ্য (Learning Objectives)',
				'content'       => '<p>By the end of this lesson, you will be able to:</p>
<ul>
  <li><strong>Comprehension:</strong> Trace the historical milestones of Nelson Mandela\'s struggle against apartheid to becoming South Africa\'s first black president in 1994.</li>
  <li><strong>Vocabulary Power:</strong> Master 6 core contextual terms (<em>apartheid, emancipation, reconciliation, icon, discrimination, resilience</em>) with pronunciation, English definitions, and Bangla meanings.</li>
  <li><strong>Grammar Focus:</strong> Identify and apply past narrative tenses (Simple Past vs. Past Perfect) and non-defining relative clauses in historical accounts.</li>
  <li><strong>Integrated Practice:</strong> Answer text-based guided questions, practice listening to historic speech audio, participate in speaking tasks, and compose a 120-word analytical paragraph.</li>
</ul>',
				'meta_data'     => array(
					'estimated_time' => '35 mins',
					'subject'        => 'English 1st Paper',
					'target_level'   => 'SSC / Class 9-10',
				),
			),

			// 2. Warm-up
			array(
				'activity_type' => NCTB_Lesson_Activity_Types::TYPE_WARMUP,
				'title'         => 'ওয়ার্ম-আপ ও ব্রেনস্টর্মিং (Warm-up Activity)',
				'content'       => '<p>Consider this famous quote by Nelson Mandela:</p>
<blockquote class="nctb-quote">"Education is the most powerful weapon which you can use to change the world."<cite>— Nelson Mandela (1918–2013)</cite></blockquote>
<p><strong>Reflection Questions:</strong></p>
<ol>
  <li>Why did Mandela choose <em>education</em> and <em>forgiveness</em> instead of revenge after spending 27 years in prison?</li>
  <li>How can peaceful determination overcome deeply entrenched racial discrimination?</li>
</ol>
<p><em>Take 2 minutes to think about these questions or discuss with your study partner before reading the passage below.</em></p>',
				'meta_data'     => array(),
			),

			// 3. Reading Passage
			array(
				'activity_type' => NCTB_Lesson_Activity_Types::TYPE_READING,
				'title'         => 'মূল পাঠ (Main Reading Passage: Nelson Mandela)',
				'content'       => '<div class="nctb-reading-passage">
  <p><span class="nctb-p-num">1</span> <strong>JOHANNESBURG (Reuters)</strong> — Nelson Mandela guided South Africa from the shackles of apartheid to a multi-racial democracy, embodying the struggle for liberation around the world. Imprisoned for nearly three decades for his fight against white minority rule, Mandela never lost his resolve to fight for his people\'s emancipation. He was determined to bring down apartheid while avoiding a civil war. His prestige and charisma helped him win the support of the world.</p>
  
  <p><span class="nctb-p-num">2</span> "I hate race discrimination most intensely and in all its manifestations. I have fought it all during my life; I will fight it now, and will do so until the end of my days," Mandela said in his acceptance speech on becoming South Africa\'s first black president in 1994. "The time for the healing of the wounds has come. The moment to bridge the chasms that divide us has come. We have, at last, achieved our political emancipation."</p>

  <p><span class="nctb-p-num">3</span> In 1993, Mandela was awarded the <strong>Nobel Peace Prize</strong>, an honor he shared with F.W. de Klerk, the white African leader who had freed him from prison three years earlier and negotiated the end of apartheid. Mandela went on to play a prominent role on the world stage as an advocate of human dignity in the face of challenges ranging from political repression to global inequality.</p>

  <p><span class="nctb-p-num">4</span> Formally leaving public life in June 2004 before his 86th birthday, he told his adoring countrymen: "Don\'t call me, I\'ll call you." But he remained one of the world\'s most revered public figures, combining celebrity sparkle with an unwavering message of freedom, respect, and universal human rights.</p>
</div>',
				'meta_data'     => array(
					'word_count' => 275,
					'source'     => 'NCTB English For Today (SSC / Class 9-10)',
					'unit'       => 'Unit 1: People and Relationships',
				),
			),

			// 4. Vocabulary
			array(
				'activity_type' => NCTB_Lesson_Activity_Types::TYPE_VOCABULARY,
				'title'         => 'শব্দার্থ ও শব্দভাণ্ডার (Vocabulary & Word Power)',
				'content'       => '<p>Learn and master the six critical academic vocabulary words from this reading passage. Review each card for pronunciation, English definitions, and contextual Bangla meanings.</p>',
				'meta_data'     => array(
					'words' => array(
						array(
							'term'          => 'Apartheid',
							'pronunciation' => '/əˈpɑːt.heɪt/',
							'pos'           => 'noun',
							'meaning_en'    => 'A policy or system of racial segregation and political discrimination.',
							'meaning_bn'    => 'বর্ণবৈষম্য নীতি (জাতিগত বিভাজন ও বৈষম্যমূলক রাষ্ট্রীয় ব্যবস্থা)',
							'example'       => 'Mandela dedicated his entire life to dismantling the cruel system of apartheid in South Africa.',
						),
						array(
							'term'          => 'Emancipation',
							'pronunciation' => '/iˌmæn.sɪˈpeɪ.ʃən/',
							'pos'           => 'noun',
							'meaning_en'    => 'The process of being set free from legal, social, or political restrictions.',
							'meaning_bn'    => 'মুক্তি / দাসত্বমুক্তি (অধিকারহীনতা বা পরাধীনতা থেকে নিষ্কৃতি)',
							'example'       => 'In 1994, South Africans finally celebrated the political emancipation of their nation.',
						),
						array(
							'term'          => 'Reconciliation',
							'pronunciation' => '/ˌrek.ənˌsɪl.iˈeɪ.ʃən/',
							'pos'           => 'noun',
							'meaning_en'    => 'The restoration of friendly relations and harmony between formerly conflicting groups.',
							'meaning_bn'    => 'পুনর্মিলন / সম্প্রীতি স্থাপন (বিরোধ নিষ্পত্তির মাধ্যমে সৌহার্দ্য গঠন)',
							'example'       => 'Instead of seeking revenge, Mandela chose national reconciliation to heal historical wounds.',
						),
						array(
							'term'          => 'Icon',
							'pronunciation' => '/ˈaɪ.kɒn/',
							'pos'           => 'noun',
							'meaning_en'    => 'A person or symbol regarded as a representative of worthy ideals.',
							'meaning_bn'    => 'প্রতীক / আদর্শ ব্যক্তিত্ব (শ্রদ্ধা ও অনুপ্রেরণার বিশ্বজনীন প্রতীক)',
							'example'       => 'Nelson Mandela stands as an international icon of peace, resilience, and human dignity.',
						),
						array(
							'term'          => 'Discrimination',
							'pronunciation' => '/dɪˌskrɪm.ɪˈneɪ.ʃən/',
							'pos'           => 'noun',
							'meaning_en'    => 'The unjust or prejudicial treatment of different categories of people on grounds of race or religion.',
							'meaning_bn'    => 'বৈষম্য (জাতি, বর্ণ বা ধর্মের ভিত্তিতে অন্যায় আচরণ)',
							'example'       => 'The new democratic constitution strictly prohibited any form of racial discrimination.',
						),
						array(
							'term'          => 'Resilience',
							'pronunciation' => '/rɪˈzɪl.jəns/',
							'pos'           => 'noun',
							'meaning_en'    => 'The capacity to recover quickly from difficulties; toughness and perseverance.',
							'meaning_bn'    => 'অদম্য মনোবল / সহনশীলতা (বিপদে ভেঙে না পড়ে ঘুরে দাঁড়ানোর শক্তি)',
							'example'       => 'His 27 years in prison tested but could not break his moral resilience.',
						),
					),
				),
			),

			// 5. Grammar Focus
			array(
				'activity_type' => NCTB_Lesson_Activity_Types::TYPE_GRAMMAR,
				'title'         => 'গ্রামার ও ভাষারীতি (Grammar Focus: Past Narrative Tenses)',
				'content'       => '<p>Biographical narratives in English rely heavily on <strong>Simple Past</strong>, <strong>Past Perfect</strong>, and <strong>Relative Clauses</strong> to establish chronological order.</p>
<div class="nctb-grammar-box">
  <h4>1. Simple Past vs. Past Perfect (কালের ক্রমবিন্যাস)</h4>
  <p>When two actions occurred in the past, the <em>earlier</em> action takes <strong>Past Perfect (had + V3)</strong> and the <em>later</em> action takes <strong>Simple Past (V2)</strong>.</p>
  <p class="example-line"><strong>Example from text:</strong> "F.W. de Klerk, who <u>had freed</u> [earlier] him from prison three years earlier, <u>negotiated</u> [later] the end of apartheid."</p>
</div>
<div class="nctb-grammar-box">
  <h4>2. Non-defining Relative Clauses with "Who"</h4>
  <p>Used to provide important additional biographical detail separated by commas.</p>
  <p class="example-line"><strong>Formula:</strong> <code>[Person Name], who + [action/background clause], + [main predicate]...</code></p>
  <p class="example-line"><strong>Example:</strong> "Nelson Mandela, <u>who spent 27 years on Robben Island</u>, received the Nobel Peace Prize."</p>
</div>',
				'meta_data'     => array(
					'formula'   => 'Subject + had + V3 (Earlier Past) ... Subject + V2 (Later Past)',
					'rules'     => array(
						'Use Past Perfect for the action completed before another past event.',
						'Non-defining relative clauses must be enclosed in commas.',
					),
					'structure' => 'Simple Past (V2) for sequenced historical milestones.',
				),
			),

			// 6. Worked Examples
			array(
				'activity_type' => NCTB_Lesson_Activity_Types::TYPE_EXAMPLE,
				'title'         => 'উদাহরণ ও বিশ্লেষণ (Worked Examples & Sentence Analysis)',
				'content'       => '<div class="nctb-example-card">
  <div class="example-badge">Example 1: Figurative Language & Metaphors</div>
  <p class="sentence-quote">"Nelson Mandela guided South Africa from the <strong>shackles of apartheid</strong> to a multi-racial democracy..."</p>
  <div class="analysis-box">
    <strong>Analysis:</strong> The noun <em>\'shackles\'</em> literally means metal handcuffs. Here it serves as a powerful metaphor for systemic political oppression and racial bondage.
  </div>
</div>

<div class="nctb-example-card">
  <div class="example-badge">Example 2: Rhetorical Parallelism in Speeches</div>
  <p class="sentence-quote">"The time for the <strong>healing of the wounds</strong> has come. The moment to <strong>bridge the chasms</strong> that divide us has come."</p>
  <div class="analysis-box">
    <strong>Analysis:</strong> Mandela utilizes parallel sentence rhythm (<em>"The time for... has come"</em> / <em>"The moment to... has come"</em>) to inspire national unity and emotional urgency in his inaugural address.
  </div>
</div>',
				'meta_data'     => array(),
			),

			// 7. Guided Practice
			array(
				'activity_type' => NCTB_Lesson_Activity_Types::TYPE_GUIDED_PRACTICE,
				'title'         => 'নির্দেশিত অনুশীলন (Guided Practice with Hints)',
				'content'       => '<p>Try answering the questions below on your own first. Use the <strong>Show Hint</strong> and <strong>Reveal Model Answer</strong> buttons to check your comprehension.</p>
<div class="nctb-guided-task">
  <div class="task-q">
    <strong>Question 1:</strong> Why was Nelson Mandela awarded the Nobel Peace Prize in 1993, and with whom did he share the honor?
  </div>
  <div class="interactive-hint-zone" data-task="q1">
    <button type="button" class="btn-toggle-hint">💡 Show Hint</button>
    <div class="hint-content" style="display:none;">
      <em>Hint: Review paragraph 3 of the text. Look for the white South African leader who negotiated the end of minority rule.</em>
    </div>
    <button type="button" class="btn-toggle-answer">✅ Reveal Model Answer</button>
    <div class="answer-content" style="display:none;">
      <p><strong>Model Answer:</strong> In 1993, Nelson Mandela was awarded the Nobel Peace Prize for his peaceful work to eliminate apartheid and establish a democratic society. He shared the award with F.W. de Klerk, who had released him from prison and partnered in the transition negotiations.</p>
    </div>
  </div>
</div>

<div class="nctb-guided-task">
  <div class="task-q">
    <strong>Question 2:</strong> Fill in the blank with the appropriate target vocabulary word: <br>
    <em>"The newly elected government celebrated the political ________ of the nation after decades of struggle."</em>
  </div>
  <div class="interactive-hint-zone" data-task="q2">
    <button type="button" class="btn-toggle-hint">💡 Show Hint</button>
    <div class="hint-content" style="display:none;">
      <em>Hint: Think of the noun meaning \'freedom from legal/social bondage\' or \'liberation\'.</em>
    </div>
    <button type="button" class="btn-toggle-answer">✅ Reveal Model Answer</button>
    <div class="answer-content" style="display:none;">
      <p><strong>Answer:</strong> <strong>emancipation</strong> (<em>"The newly elected government celebrated the political emancipation of the nation after decades of struggle."</em>)</p>
    </div>
  </div>
</div>',
				'meta_data'     => array(
					'hints'        => array(
						'Paragraph 3 describes the Nobel Peace Prize and F.W. de Klerk.',
						'The target vocabulary word is emancipation.',
					),
					'explanation'  => 'Guided practice scaffolds student comprehension through immediate, low-stakes self-checks.',
					'model_answer' => 'Model answers provide complete grammatical sentences aligned with NCTB marking criteria.',
				),
			),

			// 8. Independent Practice
			array(
				'activity_type' => NCTB_Lesson_Activity_Types::TYPE_INDEPENDENT_PRACTICE,
				'title'         => 'একক অনুশীলন (Independent Practice & Self-Check)',
				'content'       => '<p>Answer the following three comprehension questions in your study notebook. When complete, verify your responses against the self-evaluation checklist below:</p>
<ol class="practice-q-list">
  <li>What did Mandela mean by his famous statement: <em>"The time for the healing of the wounds has come"</em>?</li>
  <li>How many years did Mandela spend in prison, and what quality kept him committed to his people\'s freedom?</li>
  <li>Explain why Nelson Mandela is celebrated globally as an icon of peace rather than solely as a South African political leader.</li>
</ol>
<div class="nctb-self-check">
  <h4>📋 Student Self-Check Criteria:</h4>
  <label><input type="checkbox" class="nctb-check"> I answered all questions in complete, grammatically correct English sentences.</label><br>
  <label><input type="checkbox" class="nctb-check"> I cited direct textual evidence from the passage (e.g. 27 years in prison, 1994 presidency).</label><br>
  <label><input type="checkbox" class="nctb-check"> I incorporated at least two target vocabulary terms (e.g. <em>reconciliation, resilience</em>) in my explanations.</label>
</div>',
				'meta_data'     => array(),
			),

			// 9. Writing Task
			array(
				'activity_type' => NCTB_Lesson_Activity_Types::TYPE_WRITING,
				'title'         => 'লিখন দক্ষতা (Writing Task: 120-Word Paragraph)',
				'content'       => '<p>Write a coherent paragraph of <strong>100–120 words</strong> on <strong>"The Leadership Qualities of Nelson Mandela"</strong> using the facts and vocabulary from this lesson.</p>
<div class="nctb-writing-box">
  <div class="writing-guidelines">
    <h4>Writing Guidelines:</h4>
    <ul>
      <li><strong>Topic Sentence:</strong> State Mandela\'s primary identity as a transformative global leader.</li>
      <li><strong>Supporting Sentences:</strong> Discuss his resilience during 27 years of imprisonment and his pursuit of peaceful reconciliation over civil war.</li>
      <li><strong>Concluding Sentence:</strong> Summarize why his legacy continues to inspire youth around the world.</li>
    </ul>
  </div>
  <div class="interactive-model-answer">
    <button type="button" class="btn-toggle-model-answer">✨ View Model Paragraph (Board Standard)</button>
    <div class="model-answer-box" style="display:none;">
      <p><strong>Model Paragraph (115 words):</strong><br>
      Nelson Mandela is universally celebrated as one of the most visionary and resilient leaders in modern history. Throughout his lifelong struggle against the cruel system of apartheid in South Africa, he exhibited extraordinary courage and moral integrity. Even after enduring nearly three decades of imprisonment on Robben Island, Mandela emerged without bitterness, choosing national reconciliation and forgiveness over retribution. As South Africa’s first black president in 1994, he united a divided nation into a vibrant multi-racial democracy. His steadfast advocacy for human dignity earned him the 1993 Nobel Peace Prize. Today, Mandela remains an enduring global icon of peace, inspiring generations to champion freedom with unwavering resolve.</p>
    </div>
  </div>
</div>',
				'meta_data'     => array(
					'word_limit'     => '100–120 words',
					'outline'        => 'Topic Sentence -> Supporting Qualities (Resilience & Reconciliation) -> Democratic Impact -> Concluding Thought',
					'model_response' => 'Nelson Mandela is universally celebrated as one of the most visionary and resilient leaders...',
				),
			),

			// 10. Listening Activity
			array(
				'activity_type' => NCTB_Lesson_Activity_Types::TYPE_LISTENING,
				'title'         => 'শ্রবণ দক্ষতা (Listening Practice: Historic Speech Excerpt)',
				'content'       => '<p>Listen to the audio narration of Nelson Mandela’s historic 1994 Presidential Inaugural address excerpt. Adjust playback controls to practice your English listening comprehension.</p>
<div class="nctb-audio-player-card">
  <audio controls class="nctb-audio-element" style="width: 100%; margin-bottom: 10px;">
    <source src="https://upload.wikimedia.org/wikipedia/commons/e/eb/Nelson_Mandela_speech_excerpt.ogg" type="audio/ogg">
    Your browser does not support the audio element.
  </audio>
  <div class="audio-controls-row">
    <span class="audio-badge">⏱️ Duration: 1 min 15 sec</span>
    <span class="audio-badge">🗣️ Accent: Standard International English</span>
  </div>
</div>
<div class="nctb-transcript-accordion">
  <button type="button" class="btn-toggle-transcript">📜 View Audio Transcript</button>
  <div class="transcript-box" style="display:none;">
    <p><em>"We understand it still that there is no easy road to freedom. We know it well that none of us acting alone can achieve success. We must therefore act together as a united people, for national reconciliation, for nation building, for the birth of a new world. Let there be justice for all. Let there be peace for all. Never, never, and never again shall it be that this beautiful land will experience the oppression of one by another. The sun shall never set on so glorious a human achievement. God bless Africa!"</em></p>
  </div>
</div>
<div class="audio-check-q">
  <strong>Comprehension Check:</strong> According to the excerpt, what three goals must the people act together to achieve? (<em>Answer: National reconciliation, nation building, and the birth of a new world</em>).
</div>',
				'meta_data'     => array(
					'audio_url'    => 'https://upload.wikimedia.org/wikipedia/commons/e/eb/Nelson_Mandela_speech_excerpt.ogg',
					'duration'     => '1m 15s',
					'check_prompt' => 'What three goals must the people act together to achieve according to the speech?',
				),
			),

			// 11. Speaking Activity
			array(
				'activity_type' => NCTB_Lesson_Activity_Types::TYPE_SPEAKING,
				'title'         => 'কথন দক্ষতা (Speaking Practice: Describe an Inspiring Leader)',
				'content'       => '<p>Practice speaking in English for <strong>2 minutes</strong> on the prompt below. Use the talking points to structure your monologue.</p>
<div class="nctb-speaking-card">
  <div class="speaking-topic">
    <strong>Speaking Prompt:</strong> <em>"Describe a national or international leader whom you admire for their courage and principles."</em>
  </div>
  <div class="speaking-prompts-list">
    <p><strong>Suggested talking points:</strong></p>
    <ul>
      <li>Who the leader is and their historical background.</li>
      <li>What major obstacle, injustice, or crisis they confronted.</li>
      <li>How they demonstrated resilience, empathy, or vision.</li>
      <li>What personal lesson you drew from their life story.</li>
    </ul>
  </div>
  <div class="speaking-timer-box">
    <div class="timer-display" id="speaking-timer">02:00</div>
    <button type="button" class="nctb-btn-sm nctb-btn-primary" id="btn-start-speaking-timer">⏱️ Start 2-Min Timer</button>
    <button type="button" class="nctb-btn-sm nctb-btn-secondary" id="btn-reset-speaking-timer">🔄 Reset</button>
  </div>
</div>',
				'meta_data'     => array(
					'time_limit'     => '2 minutes',
					'talking_points' => array(
						'Leader identity and background',
						'The obstacle or injustice confronted',
						'Core qualities: resilience and empathy',
						'Personal takeaway and inspiration',
					),
				),
			),

			// 12. Lesson Summary
			array(
				'activity_type' => NCTB_Lesson_Activity_Types::TYPE_SUMMARY,
				'title'         => 'পাঠ সারসংক্ষেপ (Lesson Summary & Recap)',
				'content'       => '<div class="nctb-summary-card">
  <p>Congratulations on completing the core content of <strong>Lesson 1: Nelson Mandela — From Prisoner to President</strong>! Key takeaways:</p>
  <ul class="summary-points">
    <li><strong>Historical Journey:</strong> Nelson Mandela led South Africa\'s peaceful transition from racial apartheid to a multi-racial democracy after 27 years in prison.</li>
    <li><strong>Nobel Peace Prize:</strong> Awarded in 1993 alongside F.W. de Klerk for their joint commitment to peaceful transformation.</li>
    <li><strong>Core Vocabulary:</strong> <em>Apartheid</em> (segregation), <em>Emancipation</em> (liberation), <em>Reconciliation</em> (harmony), <em>Icon</em> (revered symbol), <em>Discrimination</em> (unfair treatment), <em>Resilience</em> (perseverance).</li>
    <li><strong>Grammar Mastery:</strong> Past narrative tense sequencing (Simple Past vs. Past Perfect) and non-defining relative clauses.</li>
  </ul>
</div>',
				'meta_data'     => array(),
			),

			// 13. Quiz Placeholder (Phase 5)
			array(
				'activity_type' => NCTB_Lesson_Activity_Types::TYPE_QUIZ_PLACEHOLDER,
				'title'         => 'পাঠ মূল্যায়ন কুইজ (Lesson Quiz — Phase 5 Placeholder)',
				'content'       => '<div class="nctb-quiz-placeholder-card">
  <div class="placeholder-icon">⚡</div>
  <div class="placeholder-body">
    <h3>পাঠ মূল্যায়ন কুইজ (Lesson Assessment Quiz)</h3>
    <p class="desc">৫টি বোর্ড স্ট্যান্ডার্ড বহুনির্বাচনী ও শূন্যস্থান পূরণ প্রশ্নের মাধ্যমে আপনার শিখনফল যাচাই করুন।</p>
    <div class="quiz-meta-badges">
      <span class="q-badge">📝 ৫টি প্রশ্ন (5 Practice Questions)</span>
      <span class="q-badge">⏱️ সময়: ৫ মিনিট</span>
      <span class="q-badge">🎯 টার্গেট: MCQ ও Fill in the blanks</span>
    </div>
    <div class="quiz-notice">
      <span>ℹ️ <em>Active practice question engine, instant marking, progressive hints & mistake notebook arrive in <strong>Phase 5</strong>.</em></span>
    </div>
  </div>
</div>',
				'meta_data'     => array(
					'question_count' => 5,
					'target_time'    => '5 mins',
					'note'           => 'Connects to Phase 5 Practice Engine',
				),
			),

			// 14. Tutor Placeholder (Phase 9)
			array(
				'activity_type' => NCTB_Lesson_Activity_Types::TYPE_TUTOR_PLACEHOLDER,
				'title'         => 'এআই টিউটর সহায়তা (AI Tutor — Phase 9 Placeholder)',
				'content'       => '<div class="nctb-tutor-placeholder-card">
  <div class="placeholder-icon">🤖</div>
  <div class="placeholder-body">
    <h3>এআই টিউটর সহায়তা (Contextual AI Tutor)</h3>
    <p class="desc">এই পাঠ বা নেলসন ম্যান্ডেলার ইতিহাস সম্পর্কিত যেকোনো প্রশ্ন বাংলায় বা ইংরেজিতে টিউটরকে জিজ্ঞাসা করুন।</p>
    <div class="tutor-suggested-prompts">
      <span class="prompt-chip">💬 "Explain the difference between apartheid and discrimination."</span>
      <span class="prompt-chip">💬 "How did Nelson Mandela\'s 27 years in prison shape his views on forgiveness?"</span>
      <span class="prompt-chip">💬 "Give me 3 more practice sentences using the word \'emancipation\'."</span>
    </div>
    <div class="tutor-notice">
      <span>ℹ️ <em>Context-aware conversational AI Tutor with Socratic hints arrives in <strong>Phase 9</strong>.</em></span>
    </div>
  </div>
</div>',
				'meta_data'     => array(
					'context_topic'     => 'Nelson Mandela, Apartheid, and SSC English 1st Paper Unit 1',
					'suggested_prompts' => array(
						'Explain the difference between apartheid and discrimination.',
						'How did Nelson Mandela\'s 27 years in prison shape his views on forgiveness?',
						'Give me 3 more practice sentences using the word \'emancipation\'.',
					),
				),
			),
		);
	}

	/**
	 * Seed practice questions for the prototype lesson (Phase 5).
	 *
	 * @param int $target_lesson_id Target lesson ID.
	 * @return void
	 */
	public static function maybe_seed_questions( $target_lesson_id = 0 ) {
		if ( get_option( self::QUESTIONS_SEEDED_OPTION ) ) {
			return;
		}

		if ( ! $target_lesson_id ) {
			$lessons = get_posts(
				array(
					'post_type'   => NCTB_Curriculum_CPT::CPT_LESSON,
					'post_status' => 'publish',
					'numberposts' => 1,
					'orderby'     => 'menu_order',
					'order'       => 'ASC',
				)
			);
			if ( ! empty( $lessons ) ) {
				$target_lesson_id = $lessons[0]->ID;
			}
		}

		if ( ! $target_lesson_id ) {
			return;
		}

		// Check if questions already exist for this lesson.
		$existing_q = NCTB_Practice_Data::get_lesson_questions( $target_lesson_id, false, true );
		if ( ! empty( $existing_q ) ) {
			update_option( self::QUESTIONS_SEEDED_OPTION, 1, false );
			return;
		}

		$questions = self::get_gold_standard_sample_questions( $target_lesson_id );
		$count     = 0;

		foreach ( $questions as $q_item ) {
			$options     = $q_item['options'] ?? array();
			$concept_ids = $q_item['concept_ids'] ?? array();
			unset( $q_item['options'], $q_item['concept_ids'] );

			$new_id = NCTB_Practice_Data::create_question( $q_item, $options, $concept_ids );
			if ( is_int( $new_id ) ) {
				$count++;
			}
		}

		update_option( self::QUESTIONS_SEEDED_OPTION, 1, false );
		NCTB_Logger::info( 'Seeded practice questions', array( 'lesson_id' => $target_lesson_id, 'count' => $count ) );
	}

	/**
	 * Define the 5 sample practice questions for Lesson 1.
	 *
	 * @param int $lesson_id Target lesson ID.
	 * @return array<int,array<string,mixed>>
	 */
	public static function get_gold_standard_sample_questions( $lesson_id ) {
		return array(
			// 1. MCQ Fact
			array(
				'lesson_id'           => $lesson_id,
				'question_type'       => NCTB_Question_Types::TYPE_MCQ,
				'prompt'              => 'In what year was Nelson Mandela awarded the Nobel Peace Prize?',
				'content'             => 'Refer to paragraph 3 of the reading passage on Nelson Mandela.',
				'difficulty'          => NCTB_Question_Types::DIFFICULTY_EASY,
				'correct_answer'      => 'B',
				'explanation'         => 'Nelson Mandela and F.W. de Klerk were jointly awarded the Nobel Peace Prize in 1993 for their peaceful work to dismantle apartheid.',
				'hint_1'              => 'Check paragraph 3 of the reading passage for the specific year.',
				'hint_2'              => 'The prize was awarded one year before he was elected president in 1994.',
				'hint_3'              => 'The year was 1993.',
				'source_type'         => NCTB_Question_Types::SOURCE_NCTB_TEXTBOOK,
				'verification_status' => NCTB_Question_Types::STATUS_VERIFIED,
				'sort_order'          => 1,
				'options'             => array(
					array( 'option_key' => 'A', 'option_text' => '1990', 'is_correct' => 0, 'feedback' => 'Incorrect. 1990 was the year Mandela was released from prison.' ),
					array( 'option_key' => 'B', 'option_text' => '1993', 'is_correct' => 1, 'feedback' => 'Correct! Nelson Mandela shared the Nobel Peace Prize with F.W. de Klerk in 1993.' ),
					array( 'option_key' => 'C', 'option_text' => '1994', 'is_correct' => 0, 'feedback' => 'Incorrect. In 1994, Mandela became South Africa\'s first black president.' ),
					array( 'option_key' => 'D', 'option_text' => '2004', 'is_correct' => 0, 'feedback' => 'Incorrect. In 2004, Mandela formally retired from public life.' ),
				),
			),

			// 2. MCQ Vocabulary Context
			array(
				'lesson_id'           => $lesson_id,
				'question_type'       => NCTB_Question_Types::TYPE_MCQ,
				'prompt'              => 'According to the passage, what does the term "apartheid" refer to?',
				'content'             => '"...Nelson Mandela guided South Africa from the shackles of apartheid to a multi-racial democracy..."',
				'difficulty'          => NCTB_Question_Types::DIFFICULTY_MEDIUM,
				'correct_answer'      => 'A',
				'explanation'         => 'Apartheid refers to the institutionalized system of racial segregation and white minority oppression in South Africa.',
				'hint_1'              => 'Think about the root meaning of racial division and legal oppression in South Africa.',
				'hint_2'              => 'It refers to the discriminatory system Mandela spent his life fighting against.',
				'hint_3'              => 'It is the system of racial segregation.',
				'source_type'         => NCTB_Question_Types::SOURCE_NCTB_TEXTBOOK,
				'verification_status' => NCTB_Question_Types::STATUS_VERIFIED,
				'sort_order'          => 2,
				'options'             => array(
					array( 'option_key' => 'A', 'option_text' => 'A state policy of racial segregation and white minority rule', 'is_correct' => 1, 'feedback' => 'Correct! Apartheid was South Africa\'s official system of racial segregation.' ),
					array( 'option_key' => 'B', 'option_text' => 'A peaceful multi-racial democratic government treaty', 'is_correct' => 0, 'feedback' => 'Incorrect. That describes democracy, which replaced apartheid.' ),
					array( 'option_key' => 'C', 'option_text' => 'An international trade and economic agreement', 'is_correct' => 0, 'feedback' => 'Incorrect. Apartheid was a domestic system of political oppression.' ),
					array( 'option_key' => 'D', 'option_text' => 'An annual award celebrating human rights activism', 'is_correct' => 0, 'feedback' => 'Incorrect. That relates to the Nobel Peace Prize.' ),
				),
			),

			// 3. Fill in the Blank
			array(
				'lesson_id'           => $lesson_id,
				'question_type'       => NCTB_Question_Types::TYPE_FILL_IN_BLANK,
				'prompt'              => 'Complete the sentence: Nelson Mandela was imprisoned for nearly ______ years for his fight against minority rule.',
				'content'             => 'Nelson Mandela spent nearly three decades in captivity on Robben Island.',
				'difficulty'          => NCTB_Question_Types::DIFFICULTY_MEDIUM,
				'correct_answer'      => '27 | 30 | three decades | twenty seven | twenty-seven | 27 years',
				'explanation'         => 'Nelson Mandela was imprisoned for 27 years (nearly three decades, from 1962 until 1990) before his release.',
				'hint_1'              => 'Recall the number of years Mandela spent on Robben Island (or "nearly three decades").',
				'hint_2'              => 'The exact number is twenty-seven (27).',
				'source_type'         => NCTB_Question_Types::SOURCE_NCTB_TEXTBOOK,
				'verification_status' => NCTB_Question_Types::STATUS_VERIFIED,
				'sort_order'          => 3,
			),

			// 4. Short Text Answer
			array(
				'lesson_id'           => $lesson_id,
				'question_type'       => NCTB_Question_Types::TYPE_SHORT_ANSWER,
				'prompt'              => 'Name the white South African president with whom Nelson Mandela shared the 1993 Nobel Peace Prize.',
				'content'             => 'Identify the leader who released Mandela from prison and negotiated the end of apartheid.',
				'difficulty'          => NCTB_Question_Types::DIFFICULTY_MEDIUM,
				'correct_answer'      => 'F.W. de Klerk | FW de Klerk | de Klerk | Frederik Willem de Klerk | Frederik de Klerk',
				'explanation'         => 'Mandela shared the 1993 Nobel Peace Prize with F.W. de Klerk, who negotiated the end of apartheid.',
				'hint_1'              => 'Review paragraph 3 for the name of the president.',
				'hint_2'              => 'His initials are F.W. and his last name begins with "de".',
				'source_type'         => NCTB_Question_Types::SOURCE_NCTB_TEXTBOOK,
				'verification_status' => NCTB_Question_Types::STATUS_VERIFIED,
				'sort_order'          => 4,
			),

			// 5. Error Correction
			array(
				'lesson_id'           => $lesson_id,
				'question_type'       => NCTB_Question_Types::TYPE_ERROR_CORRECTION,
				'prompt'              => 'Identify and correct the verb error: <br><em>"Nelson Mandela, who <u>have spent</u> 27 years in prison, negotiated the peace accords."</em>',
				'content'             => 'Nelson Mandela, who have spent 27 years in prison, negotiated the peace accords.',
				'difficulty'          => NCTB_Question_Types::DIFFICULTY_HARD,
				'correct_answer'      => 'had spent | spent',
				'explanation'         => 'Because the imprisonment occurred prior to the past negotiations, the correct grammatical form is the Past Perfect ("had spent") or Simple Past ("spent"), not the present perfect ("have spent").',
				'hint_1'              => 'Notice the verb "have spent". For an action completed prior to another past event, use Past Perfect.',
				'hint_2'              => 'Replace "have spent" with "had spent".',
				'source_type'         => NCTB_Question_Types::SOURCE_NCTB_TEXTBOOK,
				'verification_status' => NCTB_Question_Types::STATUS_VERIFIED,
				'sort_order'          => 5,
			),
		);
	}
}
