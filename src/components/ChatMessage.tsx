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
      <div className="flex w-full mb-4 justify-start">
        <div className="max-w-[80%] space-y-3">
          <Skeleton className="h-4 w-[250px]" />
          <Skeleton className="h-4 w-[200px]" />
          <Skeleton className="h-4 w-[150px]" />
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