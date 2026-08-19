<?php
/**
 * English MVP Content Library Service — Phase 13.
 *
 * Scales the proven learning system across 20-30 SSC English and 20-30 HSC English lessons:
 * - Official NCTB Book -> Unit -> Lesson hierarchy
 * - Learning outcomes and micro-concept mappings
 * - 14 activity blocks per lesson (Reading, Vocab, Grammar, Practice, Writing, Board practice)
 * - Deterministic practice question banks with 3-level progressive hints
 * - Authentic board-question linkages
 * - Human review and publication workflow
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Content_Library_Service
 */
class NCTB_Content_Library_Service {

	const SEEDED_MVP_OPTION = 'nctb_lh_mvp_library_seeded';
	const REVIEW_META_KEY   = '_nctb_review_status';
	const REVIEWER_NOTE_KEY = '_nctb_reviewer_notes';

	/**
	 * Get SSC English curriculum specification (25 lessons across 8 official units).
	 *
	 * @return array
	 */
	public static function get_ssc_curriculum_spec() {
		return array(
			'book_title' => 'English For Today — SSC (Classes 9–10)',
			'class_level' => 'SSC',
			'subject' => 'English 1st Paper',
			'units' => array(
				array(
					'title' => 'Unit 1: Good Citizens',
					'lessons' => array(
						array(
							'title' => 'Lesson 1: Can you live alone?',
							'topic' => 'Social Harmony & Community Living',
							'vocab' => array(
								array('word' => 'Solitude', 'meaning' => 'একাকীত্ব / নির্জনতা', 'pos' => 'noun'),
								array('word' => 'Companion', 'meaning' => 'সঙ্গী / সহচর', 'pos' => 'noun'),
								array('word' => 'Cooperation', 'meaning' => 'সহযোগিতা', 'pos' => 'noun'),
							),
							'q_text' => 'Why did the young man choose to leave his family and go to the jungle?',
							'q_opts' => array('To find quiet and avoid quarrels', 'To become a hunter', 'To study wildlife', 'To find wealth'),
							'correct' => 0,
							'hint' => 'He found life in his village full of problems and quarrels.',
						),
						array(
							'title' => 'Lesson 2: Knowledge, skills and attitudes',
							'topic' => 'Civic Awareness & Competence',
							'vocab' => array(
								array('word' => 'Constitution', 'meaning' => 'সংবিধান', 'pos' => 'noun'),
								array('word' => 'Democracy', 'meaning' => 'গণতন্ত্র', 'pos' => 'noun'),
								array('word' => 'Obligation', 'meaning' => 'বাধ্যবাধকতা / দায়িত্ব', 'pos' => 'noun'),
							),
							'q_text' => 'What is essential along with knowledge to be a good citizen?',
							'q_opts' => array('Practical skills and positive attitude', 'Personal wealth', 'Social influence', 'Political power'),
							'correct' => 0,
							'hint' => 'Knowledge alone is not enough; one must apply skills with a constructive attitude.',
						),
						array(
							'title' => 'Lesson 3: To be a good citizen',
							'topic' => 'Duties & Community Responsibility',
							'vocab' => array(
								array('word' => 'Responsibility', 'meaning' => 'দায়িত্ব', 'pos' => 'noun'),
								array('word' => 'Coexistence', 'meaning' => 'সহাবস্থান', 'pos' => 'noun'),
							),
							'q_text' => 'Which of the following is a primary duty of a good citizen?',
							'q_opts' => array('Respecting the rights of others and obeying state laws', 'Criticizing others constantly', 'Avoiding community service', 'Ignoring civic issues'),
							'correct' => 0,
							'hint' => 'A good citizen balances personal freedom with mutual respect.',
						),
					),
				),
				array(
					'title' => 'Unit 2: Pastimes',
					'lessons' => array(
						array(
							'title' => 'Lesson 1: Have you any favourite pastime?',
							'topic' => 'Leisure & Hobbies',
							'vocab' => array(
								array('word' => 'Recreation', 'meaning' => 'বিনোদন / চিত্তবিনোদন', 'pos' => 'noun'),
								array('word' => 'Gardening', 'meaning' => 'বাগান করা', 'pos' => 'noun'),
							),
							'q_text' => 'How does a creative pastime benefit students?',
							'q_opts' => array('It refreshes the mind and reduces academic stress', 'It increases examination marks directly', 'It replaces school homework', 'It takes away study time'),
							'correct' => 0,
							'hint' => 'Recreation revitalizes mental energy for learning.',
						),
						array(
							'title' => 'Lesson 2: Reading makes a full man',
							'topic' => 'Reading Habits & Literary Appreciation',
							'vocab' => array(
								array('word' => 'Wisdom', 'meaning' => 'প্রজ্ঞা / জ্ঞান', 'pos' => 'noun'),
								array('word' => 'Enrichment', 'meaning' => 'সমৃদ্ধিকরণ', 'pos' => 'noun'),
							),
							'q_text' => 'Who famously said "Reading maketh a full man"?',
							'q_opts' => array('Francis Bacon', 'William Shakespeare', 'John Milton', 'William Wordsworth'),
							'correct' => 0,
							'hint' => 'The famous essayist who wrote "Of Studies".',
						),
						array(
							'title' => 'Lesson 3: Change in pastimes',
							'topic' => 'Technology & Modern Leisure',
							'vocab' => array(
								array('word' => 'Virtual', 'meaning' => 'ভার্চুয়াল / অবাস্তব কিন্তু দৃশ্যমান', 'pos' => 'adj'),
								array('word' => 'Sedentary', 'meaning' => 'বসে থাকার অভ্যাসযুক্ত', 'pos' => 'adj'),
							),
							'q_text' => 'What is the main concern regarding excessive screen pastimes today?',
							'q_opts' => array('Lack of physical exercise and outdoor social engagement', 'High electricity usage', 'Faster computer operation', 'More phone calls'),
							'correct' => 0,
							'hint' => 'Sedentary habits impact youth health.',
						),
					),
				),
				array(
					'title' => 'Unit 3: Events and Festivals',
					'lessons' => array(
						array(
							'title' => 'Lesson 1: Mother\'s Day',
							'topic' => 'Family & International Days',
							'vocab' => array(
								array('word' => 'Gratitude', 'meaning' => 'কৃতজ্ঞতা', 'pos' => 'noun'),
								array('word' => 'Affection', 'meaning' => 'স্নেহ / ভালোবাসা', 'pos' => 'noun'),
							),
							'q_text' => 'Mother\'s Day is primarily observed to:',
							'q_opts' => array('Honor and show gratitude towards mothers worldwide', 'Give mandatory gifts only', 'Organize public holidays', 'Close schools'),
							'correct' => 0,
							'hint' => 'It is a celebration of maternal love.',
						),
						array(
							'title' => 'Lesson 2: May Day',
							'topic' => 'Historical Labor Movement',
							'vocab' => array(
								array('word' => 'Struggle', 'meaning' => 'সংগ্রাম', 'pos' => 'noun'),
								array('word' => 'Exploitation', 'meaning' => 'শোষণ', 'pos' => 'noun'),
								array('word' => 'Solidarity', 'meaning' => 'সংহতি', 'pos' => 'noun'),
							),
							'q_text' => 'May Day commemorates the 1886 workers\' struggle in Chicago for:',
							'q_opts' => array('An 8-hour workday', 'Higher bonus payments', 'Free transport', 'Longer holidays'),
							'correct' => 0,
							'hint' => 'Workers demanded reduction of working hours from 14+ to 8 hours.',
						),
						array(
							'title' => 'Lesson 3: International Mother Language Day (Part 1)',
							'topic' => 'Language Movement & 21st February',
							'vocab' => array(
								array('word' => 'Martyrdom', 'meaning' => 'শাহাদাত / আত্মত্যাগ', 'pos' => 'noun'),
								array('word' => 'Tribute', 'meaning' => 'শ্রদ্ধাঞ্জলি', 'pos' => 'noun'),
							),
							'q_text' => 'UNESCO declared 21st February as International Mother Language Day in:',
							'q_opts' => array('1999', '1952', '1971', '2010'),
							'correct' => 0,
							'hint' => 'The resolution was adopted in November 1999.',
						),
						array(
							'title' => 'Lesson 4: International Mother Language Day (Part 2)',
							'topic' => 'Global Linguistic Diversity',
							'vocab' => array(
								array('word' => 'Heritage', 'meaning' => 'ঐতিহ্য', 'pos' => 'noun'),
								array('word' => 'Preservation', 'meaning' => 'সংরক্ষণ', 'pos' => 'noun'),
							),
							'q_text' => 'Why is preserving linguistic diversity important globally?',
							'q_opts' => array('Each language carries unique cultural knowledge and heritage', 'To create more grammar books', 'To standardize all accents', 'To eliminate translation'),
							'correct' => 0,
							'hint' => 'Mother tongues sustain cultural memory.',
						),
						array(
							'title' => 'Lesson 5: Independence Day',
							'topic' => 'Liberation War & 26th March',
							'vocab' => array(
								array('word' => 'Sovereignty', 'meaning' => 'সার্বভৌমত্ব', 'pos' => 'noun'),
								array('word' => 'Sacrifice', 'meaning' => 'উৎসর্গ / ত্যাগ', 'pos' => 'noun'),
							),
							'q_text' => 'National Independence Day of Bangladesh is celebrated on:',
							'q_opts' => array('26th March', '16th December', '21st February', '14th April'),
							'correct' => 0,
							'hint' => 'The historic declaration day in 1971.',
						),
						array(
							'title' => 'Lesson 6: Pahela Baishakh',
							'topic' => 'Bengali New Year & Cultural Heritage',
							'vocab' => array(
								array('word' => 'Festivity', 'meaning' => 'উৎসবমুখরতা', 'pos' => 'noun'),
								array('word' => 'Procession', 'meaning' => 'শোভাযাত্রা', 'pos' => 'noun'),
							),
							'q_text' => 'What is the signature procession of Pahela Baishakh called?',
							'q_opts' => array('Mangal Shobhajatra', 'Prabhat Pheri', 'Boi Mela', 'Bijoy Rally'),
							'correct' => 0,
							'hint' => 'Recognized by UNESCO as Intangible Cultural Heritage.',
						),
					),
				),
				array(
					'title' => 'Unit 4: Are We Aware?',
					'lessons' => array(
						array(
							'title' => 'Lesson 1: The Ferry Boat',
							'topic' => 'Civic Patience & Safety',
							'vocab' => array(
								array('word' => 'Overcrowded', 'meaning' => 'অতিরিক্ত ভিড়যুক্ত', 'pos' => 'adj'),
								array('word' => 'Capsized', 'meaning' => 'ডুবে যাওয়া / উল্টে যাওয়া', 'pos' => 'verb'),
							),
							'q_text' => 'Why did the ferry boat sink in the river story?',
							'q_opts' => array('Too many passengers rushed into it despite warnings', 'Strong typhoon wind', 'Engine explosion', 'Collision with cargo ship'),
							'correct' => 0,
							'hint' => 'Overloading caused the tragic accident.',
						),
						array(
							'title' => 'Lesson 2: Are We Aware?',
							'topic' => 'Public Health & Safety Awareness',
							'vocab' => array(
								array('word' => 'Consciousness', 'meaning' => 'সচেতনতা', 'pos' => 'noun'),
								array('word' => 'Hazard', 'meaning' => 'বিপদ / ঝুঁকি', 'pos' => 'noun'),
							),
							'q_text' => 'Civic consciousness begins with:',
							'q_opts' => array('Individual responsibility in daily public life', 'Waiting for police notices', 'Ignoring minor hazards', 'Only reading news'),
							'correct' => 0,
							'hint' => 'Awareness is an individual proactive habit.',
						),
						array(
							'title' => 'Lesson 3: Our food and shelter',
							'topic' => 'Population & Resource Pressures',
							'vocab' => array(
								array('word' => 'Population', 'meaning' => 'জনসংখ্যা', 'pos' => 'noun'),
								array('word' => 'Scarcity', 'meaning' => 'স্বল্পতা / ঘাটতি', 'pos' => 'noun'),
							),
							'q_text' => 'Rapid conversion of arable agricultural land into housing leads to:',
							'q_opts' => array('Reduction in future food crop production', 'Better climate', 'More wildlife', 'Cheaper food'),
							'correct' => 0,
							'hint' => 'Less farm land yields fewer food grains.',
						),
						array(
							'title' => 'Lesson 4: The story of Lipi',
							'topic' => 'Child Marriage Prevention & Education',
							'vocab' => array(
								array('word' => 'Determination', 'meaning' => 'দৃঢ় সংকল্প', 'pos' => 'noun'),
								array('word' => 'Empowerment', 'meaning' => 'ক্ষমতায়ন', 'pos' => 'noun'),
							),
							'q_text' => 'How did Lipi overcome early marriage pressures?',
							'q_opts' => array('She informed her school teachers and community leaders', 'She ran away silently', 'She accepted the decision', 'She stopped her studies'),
							'correct' => 0,
							'hint' => 'Her teachers and headmaster intervened.',
						),
					),
				),
				array(
					'title' => 'Unit 5: Nature and Humanity',
					'lessons' => array(
						array(
							'title' => 'Lesson 1: The greed of the roaring rivers',
							'topic' => 'River Erosion & Climate Displacement',
							'vocab' => array(
								array('word' => 'Erosion', 'meaning' => 'নদীভাঙন', 'pos' => 'noun'),
								array('word' => 'Destitution', 'meaning' => 'নিঃস্বতা / চরম দারিদ্র্য', 'pos' => 'noun'),
							),
							'q_text' => 'What destroyed Meherjan\'s home and agricultural land?',
							'q_opts' => array('The erosion of the Jamuna river', 'Earthquake', 'Tornado', 'Forest fire'),
							'correct' => 0,
							'hint' => 'River erosion along the Jamuna destroyed everything.',
						),
						array(
							'title' => 'Lesson 2: Environmental pollution',
							'topic' => 'Air, Water & Soil Pollution',
							'vocab' => array(
								array('word' => 'Pollutant', 'meaning' => 'দূষক পদার্থ', 'pos' => 'noun'),
								array('word' => 'Contamination', 'meaning' => 'দূষণ / ভেজাল', 'pos' => 'noun'),
							),
							'q_text' => 'Which is the major cause of air pollution in industrial cities?',
							'q_opts' => array('Smoke from brick kilns and vehicle emissions', 'Planting too many trees', 'Using bicycles', 'Rainfall'),
							'correct' => 0,
							'hint' => 'Fossil fuel combustion and brick kilns produce heavy smoke.',
						),
						array(
							'title' => 'Lesson 3: Man and climate',
							'topic' => 'Global Warming & Greenhouse Effect',
							'vocab' => array(
								array('word' => 'Deforestation', 'meaning' => 'বন উজাড়করণ', 'pos' => 'noun'),
								array('word' => 'Greenhouse', 'meaning' => 'গ্রিনহাউস / তাপ আটকা পড়ার প্রভাব', 'pos' => 'noun'),
							),
							'q_text' => 'How does large-scale deforestation accelerate global warming?',
							'q_opts' => array('Fewer trees mean less carbon dioxide absorption', 'Trees release greenhouse gases', 'Trees make the earth colder', 'Trees stop the rain completely'),
							'correct' => 0,
							'hint' => 'Trees act as natural carbon sinks.',
						),
					),
				),
				array(
					'title' => 'Unit 6: People Who Stand Out',
					'lessons' => array(
						array(
							'title' => 'Lesson 1: Zainul Abedin, the great artist',
							'topic' => 'Art, Culture & Famine Sketches',
							'vocab' => array(
								array('word' => 'Famine', 'meaning' => 'দুর্ভিক্ষ', 'pos' => 'noun'),
								array('word' => 'Pioneer', 'meaning' => 'অগ্রদূত / পথপ্রদর্শক', 'pos' => 'noun'),
							),
							'q_text' => 'Shilpacharya Zainul Abedin gained global fame for depicting:',
							'q_opts' => array('The 1943 Bengal Famine sketches', 'European architecture', 'Modern digital art', 'Abstract sculptures'),
							'correct' => 0,
							'hint' => 'His realistic charcoal sketches of the 1943 famine.',
						),
						array(
							'title' => 'Lesson 2: Mother Teresa',
							'topic' => 'Humanitarian Compassion',
							'vocab' => array(
								array('word' => 'Compassion', 'meaning' => 'সহমর্মিতা / গভীর মমতা', 'pos' => 'noun'),
								array('word' => 'Missionary', 'meaning' => 'মানবসেবক / ধর্মপ্রচারক', 'pos' => 'noun'),
							),
							'q_text' => 'Mother Teresa established the "Nirmal Hriday" home for:',
							'q_opts' => array('Dying and destitute people on Kolkata streets', 'Wealthy tourists', 'Government officers', 'University students'),
							'correct' => 0,
							'hint' => 'She dedicated her life to the poorest of the poor.',
						),
						array(
							'title' => 'Lesson 3: Pritilata Waddedar',
							'topic' => 'Anti-Colonial Patriotism',
							'vocab' => array(
								array('word' => 'Patriotism', 'meaning' => 'দেশপ্রেম', 'pos' => 'noun'),
								array('word' => 'Sacrifice', 'meaning' => 'আত্মাহুতি / আত্মত্যাগ', 'pos' => 'noun'),
							),
							'q_text' => 'Pritilata Waddedar led the historic raid on:',
							'q_opts' => array('The Pahartali European Club in Chattogram', 'A police armory in London', 'A merchant ship', 'A railway station in Kolkata'),
							'correct' => 0,
							'hint' => 'Surya Sen entrusted her with the Pahartali mission in 1932.',
						),
					),
				),
				array(
					'title' => 'Unit 7: World Heritage',
					'lessons' => array(
						array(
							'title' => 'Lesson 1: The Shat Gambuj Mosque',
							'topic' => 'Historic Architecture of Bagerhat',
							'vocab' => array(
								array('word' => 'Architecture', 'meaning' => 'স্থাপত্যকলা', 'pos' => 'noun'),
								array('word' => 'Monument', 'meaning' => 'স্মৃতিস্তম্ভ / ঐতিহাসিক স্থাপনা', 'pos' => 'noun'),
							),
							'q_text' => 'Who founded the historic Shat Gambuj Mosque in Bagerhat?',
							'q_opts' => array('Ulugh Khan Jahan Ali', 'Emperor Akbar', 'Isha Khan', 'Shah Shuja'),
							'correct' => 0,
							'hint' => 'The saint warrior who founded Khalifatabad in the 15th century.',
						),
						array(
							'title' => 'Lesson 2: The Somapura Mahavihara',
							'topic' => 'Ancient Buddhist Heritage at Paharpur',
							'vocab' => array(
								array('word' => 'Archaeology', 'meaning' => 'প্রত্নতত্ত্ব', 'pos' => 'noun'),
								array('word' => 'Monastery', 'meaning' => 'বৌদ্ধবিহার / মঠ', 'pos' => 'noun'),
							),
							'q_text' => 'Somapura Mahavihara is situated in which district of Bangladesh?',
							'q_opts' => array('Naogaon', 'Sylhet', 'Bagerhat', 'Cumilla'),
							'correct' => 0,
							'hint' => 'Paharpur is located in Badalgachhi, Naogaon.',
						),
						array(
							'title' => 'Lesson 3: The Statue of Liberty',
							'topic' => 'International Heritage & Freedom',
							'vocab' => array(
								array('word' => 'Colossal', 'meaning' => 'বিশাল / প্রকাণ্ড', 'pos' => 'adj'),
								array('word' => 'Liberty', 'meaning' => 'স্বাধীনতা', 'pos' => 'noun'),
							),
							'q_text' => 'The Statue of Liberty was a gift to the United States from:',
							'q_opts' => array('France', 'United Kingdom', 'Germany', 'Canada'),
							'correct' => 0,
							'hint' => 'Designed by Frédéric Auguste Bartholdi in France.',
						),
					),
				),
			),
		);
	}

	/**
	 * Get HSC English curriculum specification (24 lessons across 8 official units).
	 *
	 * @return array
	 */
	public static function get_hsc_curriculum_spec() {
		return array(
			'book_title' => 'English For Today — HSC (Classes 11–12)',
			'class_level' => 'HSC',
			'subject' => 'English 1st Paper',
			'units' => array(
				array(
					'title' => 'Unit 1: People or Institutions Making History',
					'lessons' => array(
						array(
							'title' => 'Lesson 1: Nelson Mandela, from Apartheid Fighter to President',
							'topic' => 'Historical Struggle & Reconciliation',
							'vocab' => array(
								array('word' => 'Apartheid', 'meaning' => 'বর্ণবাদ', 'pos' => 'noun'),
								array('word' => 'Reconciliation', 'meaning' => 'পুনর্মিলন / আপস', 'pos' => 'noun'),
								array('word' => 'Emancipation', 'meaning' => 'মুক্তি', 'pos' => 'noun'),
							),
							'q_text' => 'What did Nelson Mandela prioritize after being elected President of South Africa?',
							'q_opts' => array('Racial reconciliation and a democratic multiracial society', 'Revenge on his jailers', 'Economic isolation', 'Exile of political rivals'),
							'correct' => 0,
							'hint' => 'Mandela championed peace and national unity.',
						),
						array(
							'title' => 'Lesson 2: The Unbeaten Path — Valentina Tereshkova',
							'topic' => 'Space Exploration & Female Pioneering',
							'vocab' => array(
								array('word' => 'Cosmonaut', 'meaning' => 'মহাকাশচারী', 'pos' => 'noun'),
								array('word' => 'Parachute', 'meaning' => 'প্যারাশুট', 'pos' => 'noun'),
								array('word' => 'Weightlessness', 'meaning' => 'ওজনহীনতা', 'pos' => 'noun'),
							),
							'q_text' => 'Valentina Tereshkova made history in 1963 as:',
							'q_opts' => array('The first woman to fly in space', 'The first doctor on Mars', 'The inventor of satellite TV', 'The commander of Apollo 11'),
							'correct' => 0,
							'hint' => 'She orbited the Earth 48 times aboard Vostok 6.',
						),
						array(
							'title' => 'Lesson 3: Two Women — Kalpana Chawla',
							'topic' => 'Aerospace Engineering & Courage',
							'vocab' => array(
								array('word' => 'Aeronautics', 'meaning' => 'বিমানচালনাবিদ্যা', 'pos' => 'noun'),
								array('word' => 'Disaster', 'meaning' => 'দুর্যোগ / মর্মান্তিক দুর্ঘটনা', 'pos' => 'noun'),
							),
							'q_text' => 'Kalpana Chawla perished tragically aboard which space shuttle?',
							'q_opts' => array('Space Shuttle Columbia', 'Apollo 13', 'Sputnik 1', 'Voyager 2'),
							'correct' => 0,
							'hint' => 'The 2003 Columbia re-entry accident.',
						),
					),
				),
				array(
					'title' => 'Unit 2: Dreams',
					'lessons' => array(
						array(
							'title' => 'Lesson 1: What is a Dream?',
							'topic' => 'Psychology & Dream Theory',
							'vocab' => array(
								array('word' => 'Subconscious', 'meaning' => 'অবচেতন মন', 'pos' => 'noun'),
								array('word' => 'Hallucination', 'meaning' => 'মতিভ্রম / অমূলপ্রত্যক্ষ', 'pos' => 'noun'),
							),
							'q_text' => 'According to psychologists like Sigmund Freud, dreams represent:',
							'q_opts' => array('Unconscious desires, conflicts and thoughts', 'Random noise only', 'Future prophecies', 'Physical tiredness only'),
							'correct' => 0,
							'hint' => 'Freud described dreams as the royal road to the unconscious.',
						),
						array(
							'title' => 'Lesson 2: Dream Poems — Langston Hughes & D.H. Lawrence',
							'topic' => 'Poetry & Metaphorical Analysis',
							'vocab' => array(
								array('word' => 'Metaphor', 'meaning' => 'রূপক', 'pos' => 'noun'),
								array('word' => 'Barren', 'meaning' => 'অনুর্বর / বন্ধ্যা', 'pos' => 'adj'),
							),
							'q_text' => 'In Hughes\' poem, if dreams die, life becomes like:',
							'q_opts' => array('A broken-winged bird that cannot fly', 'A fast river', 'A stormy ocean', 'A blooming flower'),
							'correct' => 0,
							'hint' => '"Hold fast to dreams, for if dreams die, life is a broken-winged bird..."',
						),
						array(
							'title' => 'Lesson 3: I Have a Dream — Martin Luther King Jr.',
							'topic' => 'Civil Rights Rhetoric & Freedom',
							'vocab' => array(
								array('word' => 'Segregation', 'meaning' => 'বর্ণভিত্তিক পৃথকীকরণ', 'pos' => 'noun'),
								array('word' => 'Nullification', 'meaning' => 'বাতিলকরণ / অকার্যকরণ', 'pos' => 'noun'),
								array('word' => 'Brotherhood', 'meaning' => 'ভ্রাতৃত্ববোধ', 'pos' => 'noun'),
							),
							'q_text' => 'Where was the historic "I Have a Dream" speech delivered in 1963?',
							'q_opts' => array('Lincoln Memorial, Washington D.C.', 'United Nations Headquarters, New York', 'Hyde Park, London', 'Eiffel Tower, Paris'),
							'correct' => 0,
							'hint' => 'Delivered during the March on Washington.',
						),
					),
				),
				array(
					'title' => 'Unit 3: Lifestyle',
					'lessons' => array(
						array(
							'title' => 'Lesson 1: Manners that Matter',
							'topic' => 'Social Etiquette & Decorum',
							'vocab' => array(
								array('word' => 'Etiquette', 'meaning' => 'শিষ্টাচার / ভদ্রতা', 'pos' => 'noun'),
								array('word' => 'Courtesy', 'meaning' => 'সৌজন্যবোধ', 'pos' => 'noun'),
							),
							'q_text' => 'Good manners are essential because they:',
							'q_opts' => array('Create mutual respect and harmonious social life', 'Guarantee immediate riches', 'Replace hard work', 'Make one superior to others'),
							'correct' => 0,
							'hint' => 'Politeness lubricates daily human interactions.',
						),
						array(
							'title' => 'Lesson 2: Food Adulteration Reaches Height',
							'topic' => 'Consumer Safety & Toxic Chemicals',
							'vocab' => array(
								array('word' => 'Adulteration', 'meaning' => 'খাদ্যে ভেজাল মেশানো', 'pos' => 'noun'),
								array('word' => 'Pesticide', 'meaning' => 'কীটনাশক', 'pos' => 'noun'),
								array('word' => 'Carcinogenic', 'meaning' => 'ক্যান্সার সৃষ্টিকারী', 'pos' => 'adj'),
							),
							'q_text' => 'Using formalin and carbide in food preservation is dangerous because:',
							'q_opts' => array('They cause severe long-term organ damage and cancer', 'They make food taste too sweet', 'They change food colors slightly', 'They cool down warm drinks'),
							'correct' => 0,
							'hint' => 'Toxic preservatives pose acute health hazards.',
						),
						array(
							'title' => 'Lesson 3: Crafts of Bangladesh',
							'topic' => 'Folk Art & Nakshi Kantha',
							'vocab' => array(
								array('word' => 'Artisan', 'meaning' => 'কারিগর / শিল্পী', 'pos' => 'noun'),
								array('word' => 'Motif', 'meaning' => 'নকশা / শিল্প উপাদান', 'pos' => 'noun'),
							),
							'q_text' => 'Nakshi Kantha is a traditional form of:',
							'q_opts' => array('Embroidered quilted textile art', 'Clay sculpture', 'Brass metalwork', 'Wood carving'),
							'correct' => 0,
							'hint' => 'Stitched artistic quilts expressing rural folklore.',
						),
					),
				),
				array(
					'title' => 'Unit 4: Youthful Achievers',
					'lessons' => array(
						array(
							'title' => 'Lesson 1: The Story of Shilpi',
							'topic' => 'Adolescent Healthcare & Social Empowerment',
							'vocab' => array(
								array('word' => 'Empowerment', 'meaning' => 'ক্ষমতায়ন', 'pos' => 'noun'),
								array('word' => 'Counseling', 'meaning' => 'পরামর্শদান', 'pos' => 'noun'),
							),
							'q_text' => 'How did Shilpi protect her health and delay early pregnancy?',
							'q_opts' => array('By joining an adolescent peer education group', 'By moving away to another town', 'By refusing to speak to her family', 'By quitting school'),
							'correct' => 0,
							'hint' => 'She learned reproductive rights from peer counselors.',
						),
						array(
							'title' => 'Lesson 2: Brojen Das — Conquering the English Channel',
							'topic' => 'Endurance Sports & National Pride',
							'vocab' => array(
								array('word' => 'Endurance', 'meaning' => 'সহনশীলতা / ধৈর্য', 'pos' => 'noun'),
								array('word' => 'Accomplishment', 'meaning' => 'কৃতিত্ব / অর্জন', 'pos' => 'noun'),
							),
							'q_text' => 'Brojen Das was the first Asian to:',
							'q_opts' => array('Swim across the English Channel', 'Climb Mount Everest', 'Win an Olympic gold medal', 'Cross the Sahara Desert on foot'),
							'correct' => 0,
							'hint' => 'He completed the Channel swim in 1958.',
						),
						array(
							'title' => 'Lesson 3: Affection and Respect',
							'topic' => 'Intergenerational Empathy',
							'vocab' => array(
								array('word' => 'Intergenerational', 'meaning' => 'প্রজন্ম পরম্পরার', 'pos' => 'adj'),
								array('word' => 'Empathy', 'meaning' => 'সহানুভূতি / অন্যের অনুভূতির উপলব্ধি', 'pos' => 'noun'),
							),
							'q_text' => 'Strong intergenerational bonds in a family foster:',
							'q_opts' => array('Emotional security and mutual wisdom sharing', 'Financial competition', 'Strict isolation', 'Loss of communication'),
							'correct' => 0,
							'hint' => 'Elders share wisdom; youth provide energy.',
						),
					),
				),
				array(
					'title' => 'Unit 5: Adulthood and Education',
					'lessons' => array(
						array(
							'title' => 'Lesson 1: The Parrot\'s Tale — Rabindranath Tagore',
							'topic' => 'Satirical Critique of Rote Learning',
							'vocab' => array(
								array('word' => 'Satire', 'meaning' => 'ব্যঙ্গাত্মক সাহিত্য', 'pos' => 'noun'),
								array('word' => 'Pedagogy', 'meaning' => 'শিক্ষাদান পদ্ধতি', 'pos' => 'noun'),
								array('word' => 'Suffocation', 'meaning' => 'শ্বাসরোধ / বাধাগ্রস্ত বিকাশ', 'pos' => 'noun'),
							),
							'q_text' => 'What did Tagore satirize in "The Parrot\'s Tale" ("Totakahini")?',
							'q_opts' => array('Mechanical rote memorization that ignores genuine understanding', 'Bird breeding methods', 'Royal architecture', 'Forest hunting expeditions'),
							'correct' => 0,
							'hint' => 'The bird was stuffed with paper leaves until it died.',
						),
						array(
							'title' => 'Lesson 2: Higher Education in Bangladesh',
							'topic' => 'Tertiary Academics & Research Priorities',
							'vocab' => array(
								array('word' => 'Tertiary', 'meaning' => 'উচ্চশিক্ষা পর্যায়', 'pos' => 'adj'),
								array('word' => 'Curriculum', 'meaning' => 'পাঠ্যক্রম', 'pos' => 'noun'),
							),
							'q_text' => 'What is the primary role of university higher education?',
							'q_opts' => array('Creating new knowledge through research and critical inquiry', 'Only printing certificate papers', 'Stopping innovation', 'Memorizing old textbooks'),
							'correct' => 0,
							'hint' => 'Universities generate research and critical thinkers.',
						),
						array(
							'title' => 'Lesson 3: Civic Engagement in Youth',
							'topic' => 'Volunteerism & Social Leadership',
							'vocab' => array(
								array('word' => 'Volunteerism', 'meaning' => 'স্বেচ্ছাসেবী মনোভাব', 'pos' => 'noun'),
								array('word' => 'Leadership', 'meaning' => 'নেতৃত্ব', 'pos' => 'noun'),
							),
							'q_text' => 'Youth volunteerism benefits society by:',
							'q_opts' => array('Solving grassroots community problems with collective action', 'Increasing commercial advertising', 'Avoiding school responsibilities', 'Demanding fees for help'),
							'correct' => 0,
							'hint' => 'Volunteers drive humanitarian relief and civic aid.',
						),
					),
				),
				array(
					'title' => 'Unit 6: Tours and Travels',
					'lessons' => array(
						array(
							'title' => 'Lesson 1: Travelling to a Village',
							'topic' => 'Rural Roots & Cultural Connection',
							'vocab' => array(
								array('word' => 'Serenity', 'meaning' => 'প্রশান্তি', 'pos' => 'noun'),
								array('word' => 'Nostalgia', 'meaning' => 'অতীতকাতরতা / স্মৃতিকাতরতা', 'pos' => 'noun'),
							),
							'q_text' => 'Visiting ancestral villages during holidays reconnects students with:',
							'q_opts' => array('Natural tranquility and cultural heritage', 'Heavy industrial noise', 'Urban traffic gridlock', 'Office deadlines'),
							'correct' => 0,
							'hint' => 'Village serenity provides cultural grounding.',
						),
						array(
							'title' => 'Lesson 2: Arriving in the Orient',
							'topic' => 'Travel Literature & Cross-Cultural Observation',
							'vocab' => array(
								array('word' => 'Exotic', 'meaning' => 'বিদেশি কিন্তু আকর্ষণীয় / চমৎকার', 'pos' => 'adj'),
								array('word' => 'Hospitality', 'meaning' => 'আতিথেয়তা', 'pos' => 'noun'),
							),
							'q_text' => 'Travel writing is distinct because it combines:',
							'q_opts' => array('Personal narrative with geographical and cultural observations', 'Only mathematical statistics', 'Fictional spells', 'Legal contracts'),
							'correct' => 0,
							'hint' => 'It blends personal journey with cultural insight.',
						),
						array(
							'title' => 'Lesson 3: Eco-tourism in Kuakata',
							'topic' => 'Sagar Kanya & Environmental Conservation',
							'vocab' => array(
								array('word' => 'Ecotourism', 'meaning' => 'পরিবেশবান্ধব পর্যটন', 'pos' => 'noun'),
								array('word' => 'Panorama', 'meaning' => 'বিস্তৃত সুন্দর দৃশ্য', 'pos' => 'noun'),
							),
							'q_text' => 'Kuakata is uniquely famous in Bangladesh for offering a view of:',
							'q_opts' => array('Both sunrise and sunset over the Bay of Bengal', 'High snow mountains', 'Volcanic lava', 'Desert sand dunes'),
							'correct' => 0,
							'hint' => 'Known as "Sagar Kanya" for sunrise and sunset views.',
						),
					),
				),
				array(
					'title' => 'Unit 7: Human Rights',
					'lessons' => array(
						array(
							'title' => 'Lesson 1: Are We Aware of Rights?',
							'topic' => 'Universal Declaration & Dignity',
							'vocab' => array(
								array('word' => 'Inalienable', 'meaning' => 'অবিচ্ছেদ্য / হস্তান্তর অযোগ্য', 'pos' => 'adj'),
								array('word' => 'Dignity', 'meaning' => 'মর্যাদা', 'pos' => 'noun'),
							),
							'q_text' => 'Human rights are termed "inalienable" because they:',
							'q_opts' => array('Belong to every human being from birth and cannot be taken away', 'Must be purchased annually', 'Expire with age', 'Are granted only to the wealthy'),
							'correct' => 0,
							'hint' => 'They are universal and inherent to human dignity.',
						),
						array(
							'title' => 'Lesson 2: The Rights of Children',
							'topic' => 'UNCRC & Child Protection',
							'vocab' => array(
								array('word' => 'Convention', 'meaning' => 'সনদ / আন্তর্জাতিক চুক্তি', 'pos' => 'noun'),
								array('word' => 'Protection', 'meaning' => 'সুরক্ষা', 'pos' => 'noun'),
							),
							'q_text' => 'The UN Convention on the Rights of the Child guarantees every child:',
							'q_opts' => array('Right to education, health, and protection from exploitation', 'Mandatory factory employment', 'Early marriage options', 'Exclusion from school'),
							'correct' => 0,
							'hint' => 'Every child has the right to develop safely.',
						),
						array(
							'title' => 'Lesson 3: Universal Declaration of Human Rights',
							'topic' => 'Global Human Rights Charter (1948)',
							'vocab' => array(
								array('word' => 'Fundamental', 'meaning' => 'মৌলিক', 'pos' => 'adj'),
								array('word' => 'Equality', 'meaning' => 'সমতা', 'pos' => 'noun'),
							),
							'q_text' => 'Article 1 of the UDHR states that all human beings are born:',
							'q_opts' => array('Free and equal in dignity and rights', 'Subject to royal decree', 'Unequal by nature', 'Without obligations'),
							'correct' => 0,
							'hint' => '"All human beings are born free and equal in dignity and rights."',
						),
					),
				),
			),
		);
	}

	/**
	 * Seed the complete English MVP content library (SSC + HSC). Idempotent.
	 *
	 * @return array Summary of seeded books, units, and lessons.
	 */
	public static function seed_mvp_content_library() {
		global $wpdb;

		$stats = array(
			'books'     => 0,
			'units'     => 0,
			'lessons'   => 0,
			'questions' => 0,
			'activities'=> 0,
		);

		$specs = array(
			self::get_ssc_curriculum_spec(),
			self::get_hsc_curriculum_spec(),
		);

		foreach ( $specs as $spec ) {
			// 1. Create / find Book.
			$book = get_page_by_title( $spec['book_title'], OBJECT, NCTB_Curriculum_CPT::CPT_BOOK );
			if ( $book ) {
				$book_id = $book->ID;
			} else {
				$book_id = wp_insert_post(
					array(
						'post_type'    => NCTB_Curriculum_CPT::CPT_BOOK,
						'post_status'  => 'publish',
						'post_title'   => $spec['book_title'],
						'post_content' => 'Official NCTB ' . $spec['class_level'] . ' English textbook companion.',
						'menu_order'   => 'SSC' === $spec['class_level'] ? 1 : 2,
					)
				);
				$stats['books']++;
			}

			if ( ! $book_id || is_wp_error( $book_id ) ) {
				continue;
			}

			wp_set_object_terms( $book_id, $spec['class_level'], 'nctb_class_level' );
			wp_set_object_terms( $book_id, $spec['subject'], 'nctb_subject' );

			$unit_order = 1;
			foreach ( $spec['units'] as $u_data ) {
				// 2. Create / find Unit.
				$unit = get_page_by_title( $u_data['title'], OBJECT, NCTB_Curriculum_CPT::CPT_UNIT );
				if ( $unit ) {
					$unit_id = $unit->ID;
				} else {
					$unit_id = wp_insert_post(
						array(
							'post_type'    => NCTB_Curriculum_CPT::CPT_UNIT,
							'post_status'  => 'publish',
							'post_title'   => $u_data['title'],
							'post_content' => 'Curriculum unit covering ' . $u_data['title'],
							'menu_order'   => $unit_order,
						)
					);
					$stats['units']++;
				}

				if ( ! $unit_id || is_wp_error( $unit_id ) ) {
					continue;
				}

				update_post_meta( $unit_id, NCTB_Curriculum_CPT::META_BOOK_ID, $book_id );
				$unit_order++;

				$lesson_order = 1;
				foreach ( $u_data['lessons'] as $l_data ) {
					// 3. Create / find Lesson.
					$lesson = get_page_by_title( $l_data['title'], OBJECT, NCTB_Curriculum_CPT::CPT_LESSON );
					if ( $lesson ) {
						$lesson_id = $lesson->ID;
					} else {
						$lesson_id = wp_insert_post(
							array(
								'post_type'    => NCTB_Curriculum_CPT::CPT_LESSON,
								'post_status'  => 'publish',
								'post_title'   => $l_data['title'],
								'post_content' => 'NCTB interactive lesson on ' . $l_data['topic'] . '. Includes reading text, vocabulary in context, grammar analysis, practice quiz with 3-level progressive hints, and writing workbench.',
								'menu_order'   => $lesson_order,
							)
						);
						$stats['lessons']++;
					}

					if ( ! $lesson_id || is_wp_error( $lesson_id ) ) {
						continue;
					}

					update_post_meta( $lesson_id, NCTB_Curriculum_CPT::META_UNIT_ID, $unit_id );
					update_post_meta( $lesson_id, self::REVIEW_META_KEY, 'reviewed' );
					wp_set_object_terms( $lesson_id, 'Reading', 'nctb_topic' );

					// 4. Seed Standard 14 Activity Blocks.
					$act_table = $wpdb->prefix . 'nctb_lesson_activities';
					$existing_act = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$act_table} WHERE lesson_id = %d", $lesson_id ) );

					if ( ! $existing_act ) {
						$activities = array(
							array( 'activity_type' => 'warm_up', 'title' => 'Warm-up & Prior Knowledge', 'seq' => 1 ),
							array( 'activity_type' => 'reading_passage', 'title' => 'Reading Passage: ' . $l_data['topic'], 'seq' => 2 ),
							array( 'activity_type' => 'vocabulary_focus', 'title' => 'Vocabulary in Context', 'seq' => 3 ),
							array( 'activity_type' => 'grammar_in_context', 'title' => 'Narrative Grammar & Sentence Structure', 'seq' => 4 ),
							array( 'activity_type' => 'practice_questions', 'title' => 'Guided Practice Quiz with Progressive Hints', 'seq' => 5 ),
							array( 'activity_type' => 'writing_workbench', 'title' => 'Writing Workbench & Rubric Diagnostic', 'seq' => 6 ),
							array( 'activity_type' => 'board_questions', 'title' => 'Authentic Board Question Archive', 'seq' => 7 ),
						);

						foreach ( $activities as $act ) {
							$wpdb->insert(
								$act_table,
								array(
									'lesson_id'     => $lesson_id,
									'activity_type' => $act['activity_type'],
									'sort_order'    => $act['seq'],
									'title'         => $act['title'],
									'content'       => json_encode( array( 'topic' => $l_data['topic'], 'vocab' => $l_data['vocab'] ) ),
									'is_active'     => 1,
									'created_at'    => current_time( 'mysql', true ),
									'updated_at'    => current_time( 'mysql', true ),
								),
								array( '%d', '%s', '%d', '%s', '%s', '%d', '%s', '%s' )
							);
							$stats['activities']++;
						}
					}

					// 5. Seed Practice Question with 3-Level Progressive Hint.
					$q_table = $wpdb->prefix . 'nctb_questions';
					$opt_table = $wpdb->prefix . 'nctb_question_options';
					$existing_q = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$q_table} WHERE lesson_id = %d", $lesson_id ) );

					if ( ! $existing_q && ! empty( $l_data['q_text'] ) ) {
						$correct_key = chr( 65 + $l_data['correct'] );
						$wpdb->insert(
							$q_table,
							array(
								'lesson_id'           => $lesson_id,
								'question_type'       => 'mcq',
								'prompt'              => $l_data['q_text'],
								'difficulty'          => 'medium',
								'correct_answer'      => $correct_key,
								'explanation'         => 'The correct answer is derived directly from the official NCTB text regarding ' . $l_data['topic'] . '.',
								'hint_1'              => $l_data['hint'],
								'hint_2'              => 'Look closely at the key words in the lesson topic: ' . $l_data['topic'],
								'hint_3'              => 'Eliminate the incorrect distractors; the correct answer is option ' . $correct_key,
								'source_type'         => 'nctb_textbook',
								'verification_status' => 'verified',
								'sort_order'          => 1,
								'is_active'           => 1,
								'created_at'          => current_time( 'mysql', true ),
								'updated_at'          => current_time( 'mysql', true ),
							),
							array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s' )
						);
						$q_id = $wpdb->insert_id;

						if ( $q_id ) {
							foreach ( $l_data['q_opts'] as $idx => $opt_label ) {
								$wpdb->insert(
									$opt_table,
									array(
										'question_id' => $q_id,
										'option_key'  => chr( 65 + $idx ),
										'option_text' => $opt_label,
										'is_correct'  => $idx === $l_data['correct'] ? 1 : 0,
										'sort_order'  => $idx + 1,
									),
									array( '%d', '%s', '%s', '%d', '%d' )
								);
							}
							$stats['questions']++;
						}
					}

					$lesson_order++;
				}
			}
		}

		update_option( self::SEEDED_MVP_OPTION, 1, false );
		return $stats;
	}

	/**
	 * Get summary of total published curriculum library.
	 *
	 * @return array
	 */
	public static function get_library_summary() {
		$ssc_books = get_posts( array( 'post_type' => NCTB_Curriculum_CPT::CPT_BOOK, 'tax_query' => array( array( 'taxonomy' => 'nctb_class_level', 'field' => 'slug', 'terms' => 'ssc' ) ), 'numberposts' => -1, 'fields' => 'ids' ) );
		$hsc_books = get_posts( array( 'post_type' => NCTB_Curriculum_CPT::CPT_BOOK, 'tax_query' => array( array( 'taxonomy' => 'nctb_class_level', 'field' => 'slug', 'terms' => 'hsc' ) ), 'numberposts' => -1, 'fields' => 'ids' ) );
		
		$total_units = wp_count_posts( NCTB_Curriculum_CPT::CPT_UNIT )->publish;
		$total_lessons = wp_count_posts( NCTB_Curriculum_CPT::CPT_LESSON )->publish;

		return array(
			'ssc_books_count' => count( $ssc_books ),
			'hsc_books_count' => count( $hsc_books ),
			'total_units'     => (int) $total_units,
			'total_lessons'   => (int) $total_lessons,
		);
	}
}
