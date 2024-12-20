import React from 'react';
import { cn } from '@/lib/utils';
import { Skeleton } from '@/components/ui/skeleton';
import ReactMarkdown from 'react-markdown';

interface ChatMessageProps {
  message: string;
  isUser: boolean;
  isLoading?: boolean;
}

export const ChatMessage: React.FC<ChatMessageProps> = ({ message, isUser, isLoading }) => {
  if (isLoading) {
    return (
      <div className={cn(
        "flex w-full mb-4",
        "justify-start"
      )}>
        <div className={cn(
          "max-w-[80%] rounded-lg p-4",
          "bg-gray-100 border border-gray-200 shadow-sm"
        )}>
          <div className="flex flex-col gap-2">
            <div className="text-gray-900 font-medium">
              Generating...
            </div>
            <div className="flex items-center space-x-2 text-black font-medium">
              <div>Processing</div>
              <div className="animate-[bounce_1s_infinite]">.</div>
              <div className="animate-[bounce_1s_infinite_200ms]">.</div>
              <div className="animate-[bounce_1s_infinite_400ms]">.</div>
            </div>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className={cn(
      "flex w-full mb-4",
      isUser ? "justify-end" : "justify-start"
    )}>
      <div className={cn(
        "max-w-[80%] rounded-lg p-4 prose prose-sm dark:prose-invert",
        isUser ? "bg-primary text-primary-foreground" : "bg-muted"
      )}>
        {isUser ? (
          message
        ) : (
          <ReactMarkdown>{message}</ReactMarkdown>
        )}
      </div>
    </div>
  );
};