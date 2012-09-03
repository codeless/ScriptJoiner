<?php

class FileLexer extends PHPParser_Lexer {
    protected $fileName;

    public function startLexing($fileName) {
        if (!file_exists($fileName)) {
            throw new InvalidArgumentException(sprintf('File "%s" does not exist', $fileName));
        }

        $this->fileName = $fileName;
        parent::startLexing(file_get_contents($fileName));
    }
}
