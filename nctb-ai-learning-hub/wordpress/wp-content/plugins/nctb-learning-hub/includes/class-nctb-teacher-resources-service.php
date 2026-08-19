<?php
/**
 * Teacher Downloadable Resources & Lesson Plan Repository (Phase 24).
 *
 * Provides ready-to-use 45-minute lesson plans, print-ready classroom quiz handouts,
 * slide deck outlines, and assessment rubrics for Bangladeshi educators.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_Teacher_Resources_Service
 */
class NCTB_Teacher_Resources_Service {

	/**
	 * Get all curated classroom resources with optional filtering.
	 *
	 * @param array<string,string> $args Filter arguments (subject, class, type).
	 * @return array<int,array<string,mixed>>
	 */
	public static function get_resources( $args = array() ) {
		$all = self::get_all_resources();

		if ( empty( $args ) ) {
			return $all;
		}

		return array_values(
			array_filter(
				$all,
				function( $item ) use ( $args ) {
					if ( ! empty( $args['subject'] ) && strcasecmp( $item['subject'], $args['subject'] ) !== 0 ) {
						return false;
					}
					if ( ! empty( $args['class'] ) && $item['class'] !== $args['class'] ) {
						return false;
					}
					if ( ! empty( $args['type'] ) && $item['type'] !== $args['type'] ) {
						return false;
					}
					return true;
				}
			)
		);
	}

	/**
	 * Get resource by unique ID.
	 *
	 * @param string $id Resource ID.
	 * @return array<string,mixed>|null
	 */
	public static function get_resource( $id ) {
		$all = self::get_all_resources();
		foreach ( $all as $r ) {
			if ( $r['id'] === $id ) {
				return $r;
			}
		}
		return null;
	}

	/**
	 * Repository of classroom teaching resources.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	protected static function get_all_resources() {
		return array(
			// 1. English 45-min Lesson Plan
			array(
				'id'          => 'lp_eng_modifiers',
				'title'       => '৪৫ মিনিটের লেসন প্ল্যান: HSC English Modifiers',
				'subject'     => 'English',
				'class'       => 'class_11',
				'type'        => 'lesson_plan',
				'duration'    => '45 mins',
				'description' => 'প্রি-মডিফায়ার ও পোস্ট-মডিফায়ারের নিয়ম এবং বোর্ডে আসা প্যাসেজ সমাধানের ধাপে ধাপে পাঠ পরিকল্পনা।',
				'content'     => "### 📋 ৪৫ মিনিটের পাঠ পরিকল্পনা (Lesson Plan)\n\n" .
					"**বিষয়:** HSC English 2nd Paper (Question 9: Modifiers)\n" .
					"**শ্রেণি:** একাদশ-দ্বাদশ | **সময়:** ৪৫ মিনিট | **লক্ষ্য:** ১০০% শিক্ষার্থী প্রি ও পোস্ট মডিফায়ার চিহ্নিত করে শূন্যস্থান পূরণ করতে পারবে।\n\n" .
					"----\n\n" .
					"#### ⏱️ ধাপ ১: পূর্বজ্ঞান যাচাই ও মোটিভেশন (০-৭ মিনিট)\n" .
					"- বোর্ডে লিখুন: `He is a ______ boy.` এবং শিক্ষার্থীদের বিভিন্ন ধরণের শব্দ (good, tall, school) বসাতে বলুন।\n" .
					"- ব্যাখ্যা করুন কীভাবে এই শব্দগুলো 'boy' নাউনটিকে modify বা বিশেষায়িত করছে।\n\n" .
					"#### ⏱️ ধাপ ২: মূল শিক্ষাদান (৮-২২ মিনিট)\n" .
					"- **Pre-modifiers:** Adjective, Noun Adjective, Participle, Determiner, Intensifier ('really', 'very')।\n" .
					"- **Post-modifiers:** Prepositional Phrase, Appositive, Infinitive Phrase ('to + verb')।\n" .
					"- বোর্ডে ২টি বাস্তব বোর্ড প্রশ্ন উদাহরণ লিখে নিয়মগুলো চিহ্নিত করান।\n\n" .
					"#### ⏱️ ধাপ ৩: দলগত / জোড়ায় অনুশীলন (২৩-৩৫ মিনিট)\n" .
					"- শিক্ষার্থীদের ৫টি শূন্যস্থান সংবলিত একটি ছোট প্যাসেজ দিন।\n" .
					"- জোড়ায় বসে ব্র্যাকেটের নির্দেশনা (e.g. *use a participle to pre-modify the noun*) দেখে উত্তর লিখতে বলুন।\n\n" .
					"#### ⏱️ ধাপ ৪: মূল্যায়ন ও বাড়ির কাজ (৩৬-৪৫ মিনিট)\n" .
					"- ৩ জন শিক্ষার্থীকে বোর্ডে এসে উত্তর লিখতে বলুন ও ফিডব্যাক দিন।\n" .
					"- **বাড়ির কাজ:** ঢাকা বোর্ড ২০২৩ এর মডিফায়ার প্রশ্ন সমাধান করে আনা।",
			),

			// 2. Math Classroom Quiz Handout (Print-Ready)
			array(
				'id'          => 'ws_math_algebra',
				'title'       => 'ক্লাসরুম কুইজ হ্যান্ডআউট: SSC সাধারণ গণিত (বীজগাণিতিক রাশি)',
				'subject'     => 'General Mathematics',
				'class'       => 'class_10',
				'type'        => 'worksheet',
				'duration'    => '20 mins',
				'description' => 'ফটোস্ট্যাট ও ক্লাসরুম পরীক্ষার জন্য রেডিমেড ২০ নম্বরের সংক্ষিপ্ত পরীক্ষা হ্যান্ডআউট।',
				'content'     => "<div class=\"nctb-print-exam-sheet\">
<div style=\"text-align:center; border-bottom:2px solid #000; padding-bottom:10px; margin-bottom:15px;\">
  <h2 style=\"margin:0;\">মডেল হাই স্কুল ও কলেজ</h2>
  <h3 style=\"margin:5px 0;\">শ্রেণি মূল্যায়ন পরীক্ষা — সাধারণ গণিত (অধ্যায় ৩)</h3>
  <p style=\"margin:0;\">সময়: ২০ মিনিট | পূর্ণমান: ২০</p>
</div>
<div style=\"display:flex; justify-content:space-between; margin-bottom:15px;\">
  <div>শিক্ষার্থীর নাম: _______________________</div>
  <div>রোল: ________</div>
  <div>শাখা: _____</div>
</div>
<ol style=\"line-height:2;\">
  <li>যদি \(x + \frac{1}{x} = 3\) হয়, তবে \(x^2 + \frac{1}{x^2}\) এর মান নির্ণয় করো। [মান: ৪]</li>
  <li>\(a + b = \sqrt{7}\) এবং \(a - b = \sqrt{5}\) হলে প্রমাণ করো যে, \(8ab(a^2 + b^2) = 24\)। [মান: ৬]</li>
  <li>উৎপাদকে বিশ্লেষণ করো: \(x^3 + 6x^2 + 11x + 6\)। [মান: ৫]</li>
  <li>সরল করো: \(\frac{(a+b)^2 - (a-b)^2}{(a+b)^2 + (a-b)^2}\)। [মান: ৫]</li>
</ol>
</div>",
			),

			// 3. ICT Presentation Slide Outline
			array(
				'id'          => 'sl_ict_logic',
				'title'       => 'ক্লাসরুম স্লাইড আউটলাইন: HSC ICT লজিক গেইট ও সত্যক সারণী',
				'subject'     => 'ICT',
				'class'       => 'class_11',
				'type'        => 'slides',
				'duration'    => '45 mins',
				'description' => 'প্রজেক্টর বা মাল্টিমিডিয়া ক্লাসরুমে উপস্থাপনের জন্য তৈরি স্লাইড ও শিক্ষকের নোট।',
				'content'     => "### 📽️ মাল্টিমিডিয়া স্লাইড আউটলাইন (Presentation Slides)\n\n" .
					"#### 🖥️ Slide 1: শিরোনাম\n" .
					"- **শিরোনাম:** ডিজিটাল ডিভাইস: মৌলিক ও সার্বজনীন লজিক গেইট\n" .
					"- *শিক্ষকের নোট:* শিক্ষার্থীদের প্রশ্ন করুন—কম্পিউটার কীভাবে ০ ও ১ দিয়ে সিদ্ধান্ত নেয়?\n\n" .
					"#### 🖥️ Slide 2: মৌলিক লজিক গেইট (AND, OR, NOT)\n" .
					"- **AND Gate:** সুইচিং সার্কিট (শ্রেণি সংযোগ) — $Y = A \\cdot B$\n" .
					"- **OR Gate:** সুইচিং সার্কিট (সমান্তরাল সংযোগ) — $Y = A + B$\n" .
					"- **NOT Gate:** ইনভার্টার — $Y = \\overline{A}$\n" .
					"- *শিক্ষকের নোট:* বোর্ডে সার্কিট ডায়াগ্রাম এঁকে বাল্ব জ্বলা-নেভার উদাহরণ দিন।\n\n" .
					"#### 🖥️ Slide 3: সার্বজনীন গেইট (Universal Gates: NAND & NOR)\n" .
					"- কেন সার্বজনীন বলা হয়? (যেকোনো সার্কিট বাস্তবায়নের ক্ষমতা)\n" .
					"- ডিমরগ্যানের সূত্রের প্রয়োগ: $\\overline{A + B} = \\overline{A} \\cdot \\overline{B}$\n\n" .
					"#### 🖥️ Slide 4: ক্লাসরুম অনুশীলন\n" .
					"- NAND গেইট দিয়ে OR গেইট তৈরি করার সত্যক সারণী আঁকুন।",
			),

			// 4. Assessment Rubric
			array(
				'id'          => 'rb_science_creative',
				'title'       => 'সৃজনশীল প্রশ্ন মূল্যায়ন রুব্রিক্স (বিজ্ঞান ও গণিত CQ Marking Guide)',
				'subject'     => 'Physics',
				'class'       => 'class_10',
				'type'        => 'rubric',
				'duration'    => 'N/A',
				'description' => 'সৃজনশীল প্রশ্নের ক, খ, গ, ঘ অংশের সঠিক নম্বর প্রদান ও নির্ভুল মূল্যায়নের জন্য বোর্ড স্ট্যান্ডার্ড রুব্রিক।',
				'content'     => "### 📝 সৃজনশীল প্রশ্ন (CQ) ১০ নম্বরের বোর্ড মূল্যায়ন রুব্রিক্স\n\n" .
					"| অংশ | স্তর | পূর্ণমান | মূল্যায়নের মানদণ্ড (Marking Criteria) |\n" .
					"|---|---|---|---|\n" .
					"| **(ক)** | জ্ঞানমূলক | ১ | পাঠ্যবইয়ের সঠিক সংজ্ঞা বা সূত্রের জন্য পূর্ণ ১ নম্বর। আংশিক উত্তরে ০। |\n" .
					"| **(খ)** | অনুধাবনমূলক | ২ | সঠিক তথ্য/সংজ্ঞা লিখলে ১, কারণ বা তাৎপর্যসহ ব্যাখ্যা করলে পূর্ণ ২। |\n" .
					"| **(গ)** | প্রয়োগমূলক | ৩ | ১ নম্বর: সঠিক সূত্র উল্লেখ; ২ নম্বর: মান বসানো; ৩ নম্বর: এককসহ সঠিক চূড়ান্ত মান। |\n" .
					"| **(ঘ)** | উচ্চতর দক্ষতা | ৪ | ১ নম্বর: সূত্রের সম্পর্ক; ২ নম্বর: গাণিতিক গণনা; ৩ নম্বর: শর্ত তুলনা; ৪ নম্বর: যুক্তিযুক্ত যৌক্তিক সিদ্ধান্ত। |",
			),
		);
	}
}
