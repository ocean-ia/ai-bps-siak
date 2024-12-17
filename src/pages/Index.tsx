import { useState } from 'react';
import { ChatInput } from '@/components/ChatInput';
import { ChatMessage } from '@/components/ChatMessage';
import { useToast } from '@/components/ui/use-toast';

interface Message {
  text: string;
  isUser: boolean;
}

const Index = () => {
  const [messages, setMessages] = useState<Message[]>([]);
  const [isLoading, setIsLoading] = useState(false);
  const { toast } = useToast();

  const handleSendMessage = async (message: string) => {
    try {
      setIsLoading(true);
      // Add user message to chat
      setMessages(prev => [...prev, { text: message, isUser: true }]);
      
      // Add temporary loading message
      setMessages(prev => [...prev, { text: "Generating...", isUser: false }]);

      const response = await fetch('api/chat.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ message }),
      });

      if (!response.ok) {
        throw new Error('Failed to get response from server');
      }

      const data = await response.json();
      
      if (!data.success) {
        throw new Error(data.error || 'Unknown error occurred');
      }

      // Replace loading message with AI response
      setMessages(prev => prev.slice(0, -1).concat({ text: data.response, isUser: false }));
    } catch (error) {
      // Remove loading message in case of error
      setMessages(prev => prev.slice(0, -1));
      toast({
        title: "Error",
        description: "Terjadi kesalahan saat memproses pertanyaan Anda. Silakan coba lagi.",
        variant: "destructive",
      });
      console.error('Error:', error);
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-background">
      <div className="container mx-auto max-w-4xl px-4 py-8">
        <div className="text-center mb-8">
          <h1 className="text-3xl font-bold text-primary mb-2">
            Welcome to AI Data Assistant BPS Kabupaten Siak
          </h1>
          <p className="text-muted-foreground">
            Tanyakan informasi seputar data statistik Kabupaten Siak
          </p>
        </div>

        <div className="bg-white rounded-lg shadow-lg p-4">
          <div className="h-[500px] overflow-y-auto mb-4 space-y-4">
            {messages.length === 0 ? (
              <div className="text-center text-muted-foreground py-8">
                Mulai mengajukan pertanyaan tentang data BPS Kabupaten Siak
              </div>
            ) : (
              messages.map((msg, idx) => (
                <ChatMessage
                  key={idx}
                  message={msg.text}
                  isUser={msg.isUser}
                  isLoading={!msg.isUser && idx === messages.length - 1 && isLoading}
                />
              ))
            )}
          </div>
          <ChatInput 
            onSend={handleSendMessage} 
            isLoading={isLoading}
            placeholder="Contoh: Apa itu Badan Pusat Statistik?"
          />
        </div>
      </div>
    </div>
  );
};

export default Index;