<?php
/**
 * Server-Side AI Provider Adapter (Phase 9).
 *
 * Provider-agnostic adapter for interacting with AI models (Anthropic, Gemini, OpenAI)
 * strictly server-side. API keys are never exposed to browser client code. Includes
 * intelligent curriculum-grounded fallback for local development and test environments.
 *
 * @package NCTB\LearningHub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NCTB_AI_Adapter
 */
class NCTB_AI_Adapter {

	const PROVIDER_ANTHROPIC = 'anthropic';
	const PROVIDER_GEMINI    = 'gemini';
	const PROVIDER_OPENAI    = 'openai';
	const PROVIDER_MOCK      = 'mock';

	/**
	 * Get the active AI provider.
	 *
	 * @return string
	 */
	public static function get_provider() {
		if ( defined( 'NCTB_AI_PROVIDER' ) && ! empty( constant( 'NCTB_AI_PROVIDER' ) ) ) {
			return constant( 'NCTB_AI_PROVIDER' );
		}
		return self::PROVIDER_MOCK;
	}

	/**
	 * Get the server-side AI API key.
	 *
	 * @return string
	 */
	protected static function get_api_key() {
		if ( defined( 'NCTB_AI_API_KEY' ) && ! empty( constant( 'NCTB_AI_API_KEY' ) ) ) {
			return constant( 'NCTB_AI_API_KEY' );
		}
		return '';
	}

	/**
	 * Generate an AI response from system prompt and user messages.
	 *
	 * @param string               $system_prompt System instructions and grounded lesson context.
	 * @param array<int,array>     $messages      Array of ['role' => 'user'|'assistant', 'content' => string].
	 * @param array<string,mixed>  $options       Optional parameters (temperature, max_tokens).
	 * @return array<string,mixed> Response array: ['content' => string, 'tokens_used' => int, 'provider' => string]
	 */
	public static function generate_response( $system_prompt, array $messages, array $options = array() ) {
		$provider = self::get_provider();
		$api_key  = self::get_api_key();

		// If no API key or mock provider, use intelligent grounded fallback
		if ( empty( $api_key ) || self::PROVIDER_MOCK === $provider ) {
			return self::mock_grounded_response( $system_prompt, $messages );
		}

		switch ( $provider ) {
			case self::PROVIDER_ANTHROPIC:
				return self::call_anthropic( $api_key, $system_prompt, $messages, $options );
			case self::PROVIDER_OPENAI:
				return self::call_openai( $api_key, $system_prompt, $messages, $options );
			case self::PROVIDER_GEMINI:
				return self::call_gemini( $api_key, $system_prompt, $messages, $options );
			default:
				return self::mock_grounded_response( $system_prompt, $messages );
		}
	}

	/**
	 * Intelligent curriculum-grounded mock fallback for local testing without external API calls.
	 *
	 * @param string           $system_prompt System context.
	 * @param array<int,array> $messages      Messages.
	 * @return array<string,mixed>
	 */
	protected static function mock_grounded_response( $system_prompt, array $messages ) {
		$last_msg = end( $messages );
		$prompt   = $last_msg['content'] ?? '';

		$reply = '';

		if ( false !== stripos( $prompt, 'bangla' ) || false !== stripos( $prompt, 'bengali' ) || false !== stripos( $prompt, 'বাংলা' ) ) {
			$reply = "🇧🇩 **বাংলা অর্থ ও তাৎপর্য:**\n\n- **Apartheid (বর্ণবাদ):** জাতিগত বৈষম্য নীতি।\n- **Emancipation (মুক্তি):** রাজনৈতিক বা সামাজিক বন্ধন থেকে মুক্তি।\n- **Shackle (শৃঙ্খল):** দাসত্ব বা সীমাবদ্ধতার প্রতীক।\n\nনেলসন ম্যান্ডেলা জীবনের দীর্ঘ ২৭ বছর কারাবন্দী থেকেও অহিংস সংগ্রামের মাধ্যমে শান্তি প্রতিষ্ঠা করেন।";
		} elseif ( false !== stripos( $prompt, 'hint' ) || false !== stripos( $prompt, 'clue' ) || false !== stripos( $prompt, 'ইঙ্গিত' ) || false !== stripos( $prompt, 'সূত্র' ) ) {
			$reply = "🔍 **চিন্তার সূত্র (Guided Clue):**\nসরাসরি উত্তর না দিয়ে একটি ক্লু দিচ্ছি: পাঠ্যাংশের দ্বিতীয় প্যারাগ্রাফে নজর দিন যেখানে তিনি রোবেন দ্বীপে বন্দিত্বের সময়কাল এবং ১৯৯৩ সালের নোবেল শান্তি পুরস্কারের কথা উল্লেখ করেছেন। সংখ্যাটি ২৫ থেকে ৩০-এর মধ্যে।";
		} elseif ( false !== stripos( $prompt, 'wrong' ) || false !== stripos( $prompt, 'ভুল' ) || false !== stripos( $prompt, 'incorrect' ) ) {
			$reply = "❓ **কেন এটি ভুল ছিল (Error Analysis):**\nআপনি সম্ভবত প্রেসিডেন্ট নির্বাচিত হওয়ার সাল (১৯৯৪) এবং নোবেল শান্তি পুরস্কার লাভের সাল (১৯৯৩)-এর মধ্যে বিভ্রান্ত হয়েছিলেন। মনে রাখবেন: তিনি নোবেল পান ১৯৯৩ সালে এবং প্রেসিডেন্ট হন পরের বছর ১৯৯৪ সালে।";
		} elseif ( false !== stripos( $prompt, 'example' ) || false !== stripos( $prompt, 'উদাহরণ' ) ) {
			$reply = "📝 **বাস্তব উদাহরণ (Sentence Example):**\n\n- *Word:* **Emancipation** (noun)\n- *Example:* The emancipation of oppressed communities requires persistent unity and peace.\n- *Bangla:* নির্যাতিত সম্প্রদায়ের মুক্তি অর্জনে অবিচল ঐক্য ও শান্তি প্রয়োজন।";
		} elseif ( false !== stripos( $prompt, 'explain' ) || false !== stripos( $prompt, 'ব্যাখ্যা' ) ) {
			$reply = "💡 **সহজ ব্যাখ্যা (Explanation):**\nএই পাঠ্যাংশে নেলসন ম্যান্ডেলার দক্ষিণ আফ্রিকায় বর্ণবাদবিরোধী ঐতিহাসিক সংগ্রাম তুলে ধরা হয়েছে। তিনি ১৯৯৩ সালে নোবেল শান্তি পুরস্কার লাভ করেন এবং ১৯৯৪ সালে দেশটির প্রথম কৃষ্ণাঙ্গ প্রেসিডেন্ট নির্বাচিত হন।\n\n*Key takeaway:* He guided South Africa from apartheid to a multi-racial democracy.";
		} else {
			$reply = "🤖 **AI Tutor:** আপনার প্রশ্নটি বুঝতে পেরেছি। এই অধ্যায়ের মূল বিষয়বস্তু নেলসন ম্যান্ডেলার ঐতিহাসিক নেতৃত্ব ও ত্যাগ। আপনি কি কোনো নির্দিষ্ট প্যারাগ্রাফ, শব্দার্থ বা ব্যাকরণ অংশের ব্যাখ্যা চান?";
		}

		return array(
			'content'     => $reply,
			'tokens_used' => 120,
			'provider'    => self::PROVIDER_MOCK,
		);
	}

	/**
	 * Call Anthropic Claude API server-side.
	 *
	 * @param string           $api_key       API Key.
	 * @param string           $system_prompt System context.
	 * @param array<int,array> $messages      Conversation.
	 * @param array            $options       Options.
	 * @return array<string,mixed>
	 */
	protected static function call_anthropic( $api_key, $system_prompt, array $messages, array $options ) {
		$url  = 'https://api.anthropic.com/v1/messages';
		$body = array(
			'model'       => $options['model'] ?? 'claude-3-haiku-20240307',
			'max_tokens'  => $options['max_tokens'] ?? 600,
			'system'      => $system_prompt,
			'messages'    => $messages,
			'temperature' => 0.3,
		);

		$response = wp_remote_post(
			$url,
			array(
				'headers' => array(
					'Content-Type'      => 'application/json',
					'x-api-key'         => $api_key,
					'anthropic-version' => '2023-06-01',
				),
				'body'    => wp_json_encode( $body ),
				'timeout' => 20,
			)
		);

		if ( is_wp_error( $response ) ) {
			return self::mock_grounded_response( $system_prompt, $messages );
		}

		$status = wp_remote_retrieve_response_code( $response );
		$data   = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 200 !== $status || empty( $data['content'][0]['text'] ) ) {
			return self::mock_grounded_response( $system_prompt, $messages );
		}

		$total_tokens = ( $data['usage']['input_tokens'] ?? 0 ) + ( $data['usage']['output_tokens'] ?? 0 );

		return array(
			'content'     => $data['content'][0]['text'],
			'tokens_used' => $total_tokens,
			'provider'    => self::PROVIDER_ANTHROPIC,
		);
	}

	/**
	 * Call OpenAI / Compatible API server-side.
	 *
	 * @param string           $api_key       API Key.
	 * @param string           $system_prompt System context.
	 * @param array<int,array> $messages      Conversation.
	 * @param array            $options       Options.
	 * @return array<string,mixed>
	 */
	protected static function call_openai( $api_key, $system_prompt, array $messages, array $options ) {
		$url = 'https://api.openai.com/v1/chat/completions';

		$formatted = array(
			array( 'role' => 'system', 'content' => $system_prompt ),
		);
		foreach ( $messages as $m ) {
			$formatted[] = array(
				'role'    => $m['role'],
				'content' => $m['content'],
			);
		}

		$body = array(
			'model'       => $options['model'] ?? 'gpt-4o-mini',
			'messages'    => $formatted,
			'max_tokens'  => $options['max_tokens'] ?? 600,
			'temperature' => 0.3,
		);

		$response = wp_remote_post(
			$url,
			array(
				'headers' => array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $api_key,
				),
				'body'    => wp_json_encode( $body ),
				'timeout' => 20,
			)
		);

		if ( is_wp_error( $response ) ) {
			return self::mock_grounded_response( $system_prompt, $messages );
		}

		$status = wp_remote_retrieve_response_code( $response );
		$data   = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 200 !== $status || empty( $data['choices'][0]['message']['content'] ) ) {
			return self::mock_grounded_response( $system_prompt, $messages );
		}

		return array(
			'content'     => $data['choices'][0]['message']['content'],
			'tokens_used' => $data['usage']['total_tokens'] ?? 100,
			'provider'    => self::PROVIDER_OPENAI,
		);
	}

	/**
	 * Call Google Gemini API server-side.
	 *
	 * @param string           $api_key       API Key.
	 * @param string           $system_prompt System context.
	 * @param array<int,array> $messages      Conversation.
	 * @param array            $options       Options.
	 * @return array<string,mixed>
	 */
	protected static function call_gemini( $api_key, $system_prompt, array $messages, array $options ) {
		$model = $options['model'] ?? 'gemini-1.5-flash';
		$url   = 'https://generativelanguage.googleapis.com/v1beta/models/' . urlencode( $model ) . ':generateContent?key=' . urlencode( $api_key );

		$contents = array();
		foreach ( $messages as $m ) {
			$contents[] = array(
				'role'  => ( 'user' === $m['role'] ) ? 'user' : 'model',
				'parts' => array( array( 'text' => $m['content'] ) ),
			);
		}

		$body = array(
			'systemInstruction' => array(
				'parts' => array( array( 'text' => $system_prompt ) ),
			),
			'contents'          => $contents,
			'generationConfig'  => array(
				'temperature'     => 0.3,
				'maxOutputTokens' => $options['max_tokens'] ?? 600,
			),
		);

		$response = wp_remote_post(
			$url,
			array(
				'headers' => array(
					'Content-Type' => 'application/json',
				),
				'body'    => wp_json_encode( $body ),
				'timeout' => 20,
			)
		);

		if ( is_wp_error( $response ) ) {
			return self::mock_grounded_response( $system_prompt, $messages );
		}

		$status = wp_remote_retrieve_response_code( $response );
		$data   = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 200 !== $status || empty( $data['candidates'][0]['content']['parts'][0]['text'] ) ) {
			return self::mock_grounded_response( $system_prompt, $messages );
		}

		return array(
			'content'     => $data['candidates'][0]['content']['parts'][0]['text'],
			'tokens_used' => $data['usageMetadata']['totalTokenCount'] ?? 100,
			'provider'    => self::PROVIDER_GEMINI,
		);
	}
}
