<?php declare(strict_types = 1);

namespace App\Utils\Assets;

use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;


class Macros extends MacroSet {



    public static function install(Compiler $compiler) {
        $me = new static($compiler);
        $me->addMacro('assets',[$me,'assetsMacro']);
        return $me;
    }

    public function assetsMacro(MacroNode $node,PhpWriter $writer): string
    {
        return $writer->write('echo $this->global->assetsPathProvider->locate(%node.word)');
    }

}
