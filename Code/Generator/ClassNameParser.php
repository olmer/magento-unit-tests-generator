<?php

declare(strict_types=1);

namespace Olmer\UnitTestsGenerator\Code\Generator;

class ClassNameParser
{
    /**
     * @param string $content
     *
     * @return string
     */
    public function parseClassName(string $content): string
    {
        $class = $namespace = '';
        $i = 0;
        $tokens = \token_get_all($content);
        $tokensCount = \count($tokens);
        for (; $i < $tokensCount; $i++) {
            if ($tokens[$i][0] === T_NAMESPACE) {
                for ($j = $i + 1; $j < $tokensCount; $j++) {
                    // T_NAME_QUALIFIED for PHP > 8.0, T_STRING as fallback
                    $qualifiedNameToken = defined('T_NAME_QUALIFIED') ? T_NAME_QUALIFIED : T_STRING;
                    if ($tokens[$j][0] === $qualifiedNameToken) {
                        $namespace .= '\\' . $tokens[$j][1];
                    } else {
                        if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                            break;
                        }
                    }
                }
            }
            if ($tokens[$i][0] === T_CLASS) {
                for ($j = $i + 1; $j < $tokensCount; $j++) {
                    if ($tokens[$j] === '{') {
                        $class = '\\' . $tokens[$i + 2][1];
                        break 2;
                    }
                }
            }
        }
        return $namespace . $class;
    }
}
