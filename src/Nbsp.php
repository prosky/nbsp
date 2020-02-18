<?php declare(strict_types=1);

namespace App\Utils;

use Nette\Utils\Strings;


class Nbsp
{
	public const
		ENGLISH_LOCALE = 'en',
		CZECH_LOCALE = 'cs';

	public const LOCALES = [
		self::ENGLISH_LOCALE,
		self::CZECH_LOCALE
	];

	public const EMPTY = '(^|$|;| |&nbsp;|\\(|\\n|>)';
	public const TASKS = [
		'short_words' => ['@%empty%(.{1,3}) @ig', "$1$2\u00A0"],
		'non_breaking_hyphen' => ["@(\\w{1})-(\\w+)@ig", "$1\u2011$2"],
		'numbers' => ["@(\\d) (\\d)@ig", "$1\u00A0$2"],
		'spaces_in_scales' => ["@(\\d) : (\\d)@ig", "$1\u00A0:\u00A0$2"],
		'ordered_number' => ["@(\\d\\.) ([0-9a-záčďéěíňóřšťúýž])@ig", "$1\u00A0$2"],
		'abbreviations' => ['@%empty%(%keys%) @ig', '$1$2\u00A0'],
		'prepositions' => ['@%empty%(%keys%) @ig', "$1$2\u00A0"],
		'conjunctions' => ['@%empty%(%keys%) @ig', "$1$2\u00A0"],
		'article' => ['@%empty%(%keys%) @ig', "$1$2\u00A0"],
		'units' => ["@(\\d) (%keys%)(^|[;\\.!:]| | |\\?|\\n|\\)|<|\\010|\\013|$)@ig", "$1\u00A0$2$3"]
	];

	public const KEYS = [
		self::CZECH_LOCALE => [
			'prepositions' => 'do|kromě|od|u|z|ze|za|proti|naproti|kvůli|vůči|nad|pod|před|za|o|pro|mezi|přes|mimo|při|na|po|v|ve|pod|před|s|za|mezi|se|si|k|je',
			'conjunctions' => 'a|i|o|u',
			'abbreviations' => 'vč.|cca.|č.|čís.|čj.|čp.|fa|fě|fy|kupř.|mj.|např.|p.|pí|popř.|př.|přib.|přibl.|sl.|str.|sv.|tj.|tzn.|tzv.|zvl.',
			'units' => 'm|m²|l|kg|h|°C|Kč|lidí|dní|%|mil'
		],
		self::ENGLISH_LOCALE => [
			'prepositions' => 'aboard|about|above|across|after|against|ahead of|along|amid|amidst|among|around|are|as|as far as|as of|aside from|at|athwart|atop|be|barring|because of|before|behind|below|beneath|beside|besides|between|beyond|but|by|by means of|circa|concerning|despite|down|during|except|except for|excluding|far from|following|for|from|is|in|in accordance with|in addition to|in case of|in front of|in lieu of|in place of|in spite of|including|inside|instead of|into|like|minus|near|next to|notwithstanding|of|off|on|on account of|on behalf of|on top of|onto|opposite|out|out of|outside|over|past|plus|prior to|regarding|regardless of|save|since|than|through|throughout|till|to|toward|towards|under|underneath|unlike|until|up|upon|versus|via|with|with regard to|within|without',
			'conjunctions' => 'and|at|even|about|or|to',
			'article' => 'a|an|the',
			'units' => 'm|m²|l|kg|h|°C|Kč|peoples|days|moths|%|miles'
		]
	];


	private $tasks = [];

	/**
	 * Nbsp constructor.
	 */
	public function __construct()
	{
		foreach (self::LOCALES as $LOCALE) {
			foreach (self::TASKS as $key => [$regex, $replacement]) {
				$regex = str_replace('%empty%', self::EMPTY, $regex);
				if (isset(self::KEYS[$LOCALE], self::KEYS[$LOCALE][$key])) {
					$regex = str_replace('%keys%', self::KEYS[$LOCALE][$key], $regex);
				}
				$this->tasks[$LOCALE][$key] = [$regex, $replacement];
			}
		}
	}

	public function nbsp(string $content, string $locale = self::CZECH_LOCALE): string
	{
		$content = self::spacelessText($content);
		foreach ($this->tasks[$locale] as $key => $TASK) {
			$content = Strings::replace($content, ...$TASK);
		}
		return trim($content);
	}

	/**
	 * Replaces all repeated white spaces with a single space.
	 * @param string $text
	 * @return string
	 */
	public static function spacelessText(string $text): string
	{
		return preg_replace('#[ \t\r\n]+#', ' ', $text);
	}

}
