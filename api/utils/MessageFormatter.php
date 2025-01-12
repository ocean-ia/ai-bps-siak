<?php
class MessageFormatter {
    public static function format($response) {
        return preg_replace(
            [
                '/\*\*(.*?)\*\*/',                
                '/(https?:\/\/[^\s<]+)/',         
                '/^\d+\.\s+(.*)$/m',              
                '/^-\s+(.*)$/m'                   
            ],
            [
                '<strong>$1</strong>',
                '<a href="$1" target="_blank" class="text-blue-500 hover:underline">$1</a>',
                '<ol class="list-decimal list-inside"><li>$1</li></ol>',
                '<ul class="list-disc list-inside"><li>$1</li></ul>'
            ],
            $response
        );
    }
}
?>