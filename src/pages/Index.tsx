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

      // This is where you would integrate with Gemini AI API
      // For now, we'll simulate a response
      const response = "Ini adalah simulasi respons dari AI. Dalam implementasi PHP, Anda akan mengintegrasikan dengan Gemini AI API untuk mendapatkan respons yang sebenarnya berdasarkan data BPS Kabupaten Siak.";
      
      // Add AI response to chat
      setMessages(prev => [...prev, { text: response, isUser: false }]);
    } catch (error) {
      toast({
        title: "Error",
        description: "Terjadi kesalahan saat memproses pertanyaan Anda. Silakan coba lagi.",
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
          <h1 className="text-3xl font-bold text-primary mb-2">
            Portal Data BPS Kabupaten Siak
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
                  isLoading={isLoading && idx === messages.length - 1}
                />
              ))
            )}
          </div>
          <ChatInput onSend={handleSendMessage} isLoading={isLoading} />
        </div>
      </div>
    </div>
  );
};

export default Index;