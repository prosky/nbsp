<?php declare(strict_types=1);

namespace App\Utils;

use DOMXPath;
use DOMDocument;
use Latte\Engine;
use Latte\Compiler;
use Latte\MacroNode;
use Latte\PhpWriter;
use Latte\Macros\MacroSet;


class NbspMacro extends MacroSet
{

	public const DEFAULT_LOCALE = Nbsp::CZECH_LOCALE;

	/** @var Nbsp */
	private $nbsp;

	/**
	 * NbspMacro constructor.
	 * @param Compiler $compiler
	 * @param Nbsp $nbsp
	 */
	public function __construct(Compiler $compiler, Nbsp $nbsp)
	{
		parent::__construct($compiler);
		$this->nbsp = $nbsp;
	}


	public static function install(Compiler $compiler): self
	{
		$me = new static($compiler, new Nbsp());
		//$me->addMacro('nbsp', [$me, 'macroNbsp'], [$me, 'macroNbsp']);
		$me->addMacro('nbsp', [$me, 'nbspStart'], [$me, 'nbspEnd']);
		$me->addMacro('nbsphtml', [$me, 'nbspStart'], [$me, 'nbspHTMLEnd']);
		//$me->addMacro('nbsp', [$me, 'nbspMacroStart'], [$me, 'nbspMacroEnd']);
		return $me;
	}

	public function filterNbspText($s, string $locale = self::DEFAULT_LOCALE): string
	{
		return $this->nbsp->nbsp($s, $locale);
	}

	public function filterNbspHtml(string $s, int $phase = null, bool &$strip = true): string
	{
		if ($phase & PHP_OUTPUT_HANDLER_START) {
			$s = ltrim($s);
		}
		if ($phase & PHP_OUTPUT_HANDLER_FINAL) {
			$s = rtrim($s);
		}
		return $this->nbspHtml($s);
	}

	public function nbspStart(MacroNode $node, PhpWriter $writer): string
	{
		return 'if (false) {';
	}

	public function nbspEnd(MacroNode $node, PhpWriter $writer): string
	{
		return '};?>' . $this->nbsp->nbsp($node->content, $node->args ?: self::DEFAULT_LOCALE) . '<?php';
	}

	public function nbspHTMLEnd(MacroNode $node, PhpWriter $writer): string
	{
		return '};?>' . $this->nbspHtml($node->content) . '<?php';
	}

	/**
	 * @param string $content
	 * @param string $locale
	 * @return string
	 * @todo hledat atribut lang
	 */
	public function nbspHtml(string $content, string $locale = self::DEFAULT_LOCALE): string
	{
		$doc = new DOMDocument('1.1', 'UTF-8');
		$doc->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
		$xpath = new DOMXPath($doc);
		$textNodes = $xpath->query('//text()');
		foreach ($textNodes as $textNode) {
			$textNode->data = $this->nbsp->nbsp($textNode->data, $locale);
		}
		$body = $doc->getElementsByTagName('body')->item(0);
		return trim(str_replace(['<body>', '</body>'], ['', ''], $doc->saveHTML($body)));
	}

	public function macroNbsp(MacroNode $node, PhpWriter $writer)
	{
		$node->openingCode = in_array($node->context[0], [Engine::CONTENT_HTML, Engine::CONTENT_XHTML], true)
			? '<?php ob_start(function ($s, $phase) { static $strip = true; return App\Utils\NbspMacro::filterNbspHtml($s, $phase, $strip); }, 4096); ?>'
			: "<?php ob_start('App\\Utils\\NbspMacro::filterNbspText', 4096); ?>";
		$node->closingCode = '<?php ob_end_flush(); ?>';
	}


}
