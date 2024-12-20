import { useState } from 'react';
import { ChatInput } from '@/components/ChatInput';
import { ChatMessage } from '@/components/ChatMessage';
import { useToast } from '@/hooks/use-toast';

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
      setMessages(prev => [...prev, { text: message, isUser: true }]);

      const apiUrl = window.location.origin + '/api/chat.php';
      console.log('Sending request to:', apiUrl);

      const response = await fetch(apiUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ message }),
      });

      if (!response.ok) {
        const errorText = await response.text();
        console.error('API Error:', errorText);
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();
      console.log('API Response:', data);
      
      if (!data.success) {
        throw new Error(data.error || 'Unknown error occurred');
      }

      setMessages(prev => [...prev, { text: data.response, isUser: false }]);
    } catch (error) {
      console.error('Detailed Error:', error);
      toast({
        title: "Error",
        description: error instanceof Error ? error.message : "Terjadi kesalahan saat memproses pertanyaan Anda. Silakan coba lagi.",
        variant: "destructive",
      });
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-background">
      <div className="container mx-auto max-w-4xl px-4 py-8">
        <div className="text-center mb-8">
          <div className="flex items-center justify-center mb-4">
            <img 
              src="./images/bps-siak-logo.png" 
              alt="BPS Kabupaten Siak Logo" 
              className="h-20 object-contain"
            />
          </div>
          <h1 className="text-3xl font-bold text-primary mb-2">
            AI Data Assistant BPS Kabupaten Siak
          </h1>
          <p className="text-muted-foreground">
            Tanyakan informasi seputar data statistik Kabupaten Siak
          </p>
        </div>

        <div className="bg-card rounded-lg shadow-lg p-4">
          <div className="h-[500px] overflow-y-auto mb-4 space-y-4">
            {messages.length === 0 ? (
              <div className="text-center text-muted-foreground py-8">
                Mulai mengajukan pertanyaan tentang data BPS Kabupaten Siak
              </div>
            ) : (
              <>
                {messages.map((msg, idx) => (
                  <ChatMessage
                    key={idx}
                    message={msg.text}
                    isUser={msg.isUser}
                  />
                ))}
                {isLoading && (
                  <ChatMessage
                    message=""
                    isUser={false}
                    isLoading={true}
                  />
                )}
              </>
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