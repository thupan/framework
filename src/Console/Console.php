<?php

namespace Console;

class Console {

    //Console::make('generate:key');

    public function make($command) {

        $command = explode(':', $command);

        switch($command[0]) {
            case 'generate':
                switch($command[1]) {
                    case 'key':
                    //
                    break;
                }
            break;
        }

    }

}
