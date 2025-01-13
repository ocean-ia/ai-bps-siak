<?php
class AIMessageFormatter {
    public static function format($response) {
        // Add double line breaks for headers and lists
        $response = preg_replace('/\n([A-Z][^\n]+:)/', "\n\n$1", $response);
        
        // Add extra line break before bullet points
        $response = preg_replace('/\n([-\*])/', "\n\n$1", $response);
        
        // Add extra line break before numbered lists
        $response = preg_replace('/\n(\d+\.)/', "\n\n$1", $response);
        
        // Convert markdown to HTML with proper spacing
        $formatted = preg_replace(
            [
                '/\*\*(.*?)\*\*/',                // Bold text
                '/\*(.*?)\*/',                    // Italic text
                '/(https?:\/\/[^\s<]+)/',         // URLs
                '/^\d+\.\s+(.*)$/m',              // Numbered lists
                '/^-\s+(.*)$/m',                  // Bullet points
                '/^#{3}\s+(.*)$/m',               // H3 headers
                '/^#{2}\s+(.*)$/m',               // H2 headers
                '/^#{1}\s+(.*)$/m',               // H1 headers
                '/`{3}(.*?)`{3}/s',               // Code blocks
                '/`([^`]+)`/',                    // Inline code
                '/\n\n+/',                        // Multiple line breaks
                '/\n/',                           // Single line breaks
            ],
            [
                '<strong>$1</strong>',
                '<em>$1</em>',
                '<a href="$1" target="_blank" class="text-blue-500 hover:underline">$1</a>',
                '<ol class="list-decimal list-inside mb-4"><li>$1</li></ol>',
                '<ul class="list-disc list-inside mb-4"><li>$1</li></ul>',
                '<h3 class="text-lg font-semibold mt-6 mb-4">$1</h3>',
                '<h2 class="text-xl font-bold mt-8 mb-4">$1</h2>',
                '<h1 class="text-2xl font-bold mt-10 mb-6">$1</h1>',
                '<pre class="bg-gray-100 p-4 rounded-lg my-4 overflow-x-auto"><code>$1</code></pre>',
                '<code class="bg-gray-100 px-2 py-1 rounded">$1</code>',
                '<br><br>',                        // Convert multiple line breaks to double <br>
                '<br>',                           // Convert single line breaks to <br>
            ],
            $response
        );

        // Handle multiple consecutive list items
        $formatted = preg_replace(
            '/<\/li><\/[ou]l>\s*<[ou]l[^>]*><li>/',
            '</li><li>',
            $formatted
        );

        return $formatted;
    }
}
?>