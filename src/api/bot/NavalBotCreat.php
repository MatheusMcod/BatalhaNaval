<?php

class BotCreat {
    public static function createBot($difficulty) {
        switch ($difficulty) {
            case 'easy':
                return new EasyBot();
            case 'medium':
                return new MediumBot();
            case 'hard':
                return new HardBot();
            default:
                throw new Exception("Dificuldade inválida.");
        }
    }
}