<?php
class AIMessageFormatter {
    public static function format($response) {
        // Add double line break for new topics/sections
        $response = preg_replace('/\n([A-Z][^\n]+:)/', "\n\n$1", $response);
        
        // Keep natural spacing for lists (single line break)
        $response = preg_replace('/\n([-\*])/', "\n$1", $response);
        $response = preg_replace('/\n(\d+\.)/', "\n$1", $response);
        
        // Convert markdown to HTML with natural spacing
        $formatted = preg_replace(
            [
                '/\*\*(.*?)\*\*/',                // Bold text
                '/\*(.*?)\*/',                    // Italic text
                '/\b(https?:\/\/[^\s]+)\b/',      // URLs
                '/^\d+\.\s+(.*)$/m',              // Numbered lists
                '/^[-\*]\s+(.*)$/m',              // Bullet points
                '/^#{3}\s+(.*)$/m',               // H3 headers
                '/^#{2}\s+(.*)$/m',               // H2 headers
                '/^#{1}\s+(.*)$/m',               // H1 headers
                '/`{3}(.*?)`{3}/s',               // Code blocks
                '/`([^`]+)`/',                    // Inline code
                '/\n\n\n+/',                      // Multiple line breaks to double
                '/\n/',                           // Single line breaks
            ],
            [
                '<strong>$1</strong>',
                '<em>$1</em>',
                '<a href="$1" target="_blank" class="text-blue-500 hover:underline">$1</a>',
                '<ol class="list-decimal list-inside"><li>$1</li></ol>',
                '<ul class="list-disc list-inside"><li>$1</li></ul>',
                '<h3 class="text-lg font-semibold mt-4 mb-2">$1</h3>',
                '<h2 class="text-xl font-bold mt-4 mb-2">$1</h2>',
                '<h1 class="text-2xl font-bold mt-4 mb-3">$1</h1>',
                '<pre class="bg-gray-100 p-4 rounded-lg my-3 overflow-x-auto"><code>$1</code></pre>',
                '<code class="bg-gray-100 px-2 py-1 rounded">$1</code>',
                '<br><br>',                       // Convert multiple line breaks to double
                '<br>',                           // Convert single line breaks
            ],
            $response
        );
        
        return $formatted;
    }
}
