<?php
/**
 * Notes & Explanations Service (Phase 18).
 *
 * Handles note retrieval, LaTeX/Math formula rendering support, related note links,
 * and default curriculum note seeding.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Notes_Service
 */
class NCTB_Notes_Service {

	/**
	 * Get formatted note data.
	 *
	 * @param int $note_id Note Post ID.
	 * @return array<string,mixed>|null
	 */
	public static function get_note( $note_id ) {
		$post = get_post( $note_id );
		if ( ! $post || NCTB_Note_CPT::POST_TYPE !== $post->post_type ) {
			return null;
		}

		$terms = wp_get_post_terms( $post->ID, NCTB_Note_CPT::TAXONOMY, array( 'fields' => 'names' ) );
		$type_name = ! empty( $terms ) && ! is_wp_error( $terms ) ? $terms[0] : 'Revision Summary';

		$lesson_id = (int) get_post_meta( $post->ID, NCTB_Note_CPT::META_LESSON_ID, true );

		return array(
			'id'         => $post->ID,
			'title'      => $post->post_title,
			'content'    => apply_filters( 'the_content', $post->post_content ),
			'excerpt'    => $post->post_excerpt,
			'type'       => $type_name,
			'class'      => get_post_meta( $post->ID, NCTB_Note_CPT::META_CLASS, true ) ?: 'all',
			'subject'    => get_post_meta( $post->ID, NCTB_Note_CPT::META_SUBJECT, true ) ?: 'English',
			'audience'   => get_post_meta( $post->ID, NCTB_Note_CPT::META_AUDIENCE, true ) ?: 'both',
			'difficulty' => get_post_meta( $post->ID, NCTB_Note_CPT::META_DIFFICULTY, true ) ?: 'medium',
			'lesson_id'  => $lesson_id,
			'permalink'  => get_permalink( $post->ID ),
		);
	}

	/**
	 * Seed sample curriculum notes if none exist.
	 *
	 * @return void
	 */
	public static function maybe_seed_notes() {
		$count = wp_count_posts( NCTB_Note_CPT::POST_TYPE );
		if ( ! empty( $count->publish ) && $count->publish > 0 ) {
			return;
		}

		// 1. Modifiers Formula Sheet
		$note1_content = '<h2>🎯 Modifiers: ১০টি গুরুত্বপূর্ণ সূত্র ও বোর্ড হ্যাক্স</h2>
<p>এইচএসসি ইংরেজি ২য় পত্রে মডিফায়ার অংশে পূর্ণ নম্বর পেতে নিচের সূত্রগুলো মুখস্থ রাখুন:</p>

<table class="nctb-table">
<thead>
<tr>
<th>মডিফায়ারের ধরন</th>
<th>স্ট্রাকচার / সূত্র</th>
<th>উদাহরণ</th>
</tr>
</thead>
<tbody>
<tr>
<td><strong>Pre-modify the noun with an adjective</strong></td>
<td>Adjective + Noun</td>
<td>It was a <em>pleasant</em> journey.</td>
</tr>
<tr>
<td><strong>Pre-modify the verb with an adverb</strong></td>
<td>Adverb + Verb</td>
<td>He <em>quickly</em> finished the task.</td>
</tr>
<tr>
<td><strong>Post-modify the verb with an infinitive</strong></td>
<td>to + Verb (Base)</td>
<td>She went to market <em>to buy</em> books.</td>
</tr>
<tr>
<td><strong>Use an appositive</strong></td>
<td>Noun Phrase identifying person/thing</td>
<td>Kazi Nazrul Islam, <em>our national poet</em>, was born in Churulia.</td>
</tr>
<tr>
<td><strong>Use a present participle</strong></td>
<td>Verb + ing</td>
<td><em>Hearing</em> the noise, the boy woke up.</td>
</tr>
</tbody>
</table>

<h3>💡 বোর্ড পরীক্ষার প্রো-টিপ:</h3>
<p>প্রশ্নপত্রে যখন <code>use a noun adjective</code> বলবে, তখন নাউনের পূর্বে আরেকটি নাউন বসিয়ে বিশেষণ হিসেবে ব্যবহার করতে হবে (উদাঃ <em>train</em> journey, <em>tea</em> garden)।</p>';

		$n1 = wp_insert_post(
			array(
				'post_title'   => 'HSC English 2nd Paper: Modifiers Rules & Formula Sheet',
				'post_content' => $note1_content,
				'post_excerpt' => 'মডিফায়ার এর ১০টি গোল্ডেন রুলস, নাউন অ্যাডজেক্টিভ ও অ্যাপজিটিভ সূত্রের রিভিশন শিট।',
				'post_status'  => 'publish',
				'post_type'    => NCTB_Note_CPT::POST_TYPE,
			)
		);
		if ( $n1 && ! is_wp_error( $n1 ) ) {
			update_post_meta( $n1, NCTB_Note_CPT::META_CLASS, 'class_11' );
			update_post_meta( $n1, NCTB_Note_CPT::META_SUBJECT, 'English 2nd Paper' );
			update_post_meta( $n1, NCTB_Note_CPT::META_AUDIENCE, 'both' );
			update_post_meta( $n1, NCTB_Note_CPT::META_DIFFICULTY, 'medium' );
			wp_set_object_terms( $n1, 'formula_sheet', NCTB_Note_CPT::TAXONOMY );
		}

		// 2. Right Form of Verbs Revision Summary
		$note2_content = '<h2>⚡ Right Form of Verbs: কন্ডিশনাল ও স্পেশাল স্ট্রাকচার চার্ট</h2>

<div class="note-highlight-box">
<h4>১. কন্ডিশনাল স্ট্রাকচার ম্যাট্রিক্স:</h4>
<ul>
<li><strong>1st Conditional:</strong> If + Present Indefinite \(\rightarrow\) Future Indefinite (Subject + will/can + V1)</li>
<li><strong>2nd Conditional:</strong> If + Past Indefinite \(\rightarrow\) Subject + would/could + V1</li>
<li><strong>3rd Conditional:</strong> If + Past Perfect \(\rightarrow\) Subject + would have + V3</li>
<li><strong>Had + Subject + V3:</strong> Had I seen him, I <em>would have told</em> him the matter.</li>
</ul>
</div>

<h4>২. কিছু বিশেষ নিয়মের চার্ট:</h4>
<ul>
<li><strong>No sooner had ... than:</strong> No sooner had the bell rung than the teacher entered the classroom.</li>
<li><strong>Lest ... should:</strong> Walk fast lest you <em>should miss</em> the train.</li>
<li><strong>It is high time / It is time:</strong> It is high time we <em>changed</em> our bad habits (V2).</li>
</ul>';

		$n2 = wp_insert_post(
			array(
				'post_title'   => 'Right Form of Verbs: Conditionals & Special Structures Matrix',
				'post_content' => $note2_content,
				'post_excerpt' => 'ফার্স্ট, সেকেন্ড ও থার্ড কন্ডিশনাল, It is high time এবং Lest এর নিয়মাবলি।',
				'post_status'  => 'publish',
				'post_type'    => NCTB_Note_CPT::POST_TYPE,
			)
		);
		if ( $n2 && ! is_wp_error( $n2 ) ) {
			update_post_meta( $n2, NCTB_Note_CPT::META_CLASS, 'all' );
			update_post_meta( $n2, NCTB_Note_CPT::META_SUBJECT, 'English Grammar' );
			update_post_meta( $n2, NCTB_Note_CPT::META_AUDIENCE, 'both' );
			update_post_meta( $n2, NCTB_Note_CPT::META_DIFFICULTY, 'foundation' );
			wp_set_object_terms( $n2, 'grammar_rule', NCTB_Note_CPT::TAXONOMY );
		}
	}
}
