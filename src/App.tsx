import React from 'react'

function App() {
  return (
    <div className="container mx-auto max-w-4xl px-4 py-8 flex-1 flex flex-col main-content">
        <div className="text-center mb-8">
            <div className="flex items-center justify-center mb-4">
                <img 
                    src="/images/bps-siak-logo.png" 
                    alt="BPS Kabupaten Siak Logo" 
                    className="h-10 w-auto object-contain"
                />
            </div>
            <h1 className="text-3xl font-bold text-blue-500 mb-2">
                AI Data Assistant
            </h1>
        </div>
    </div>
  )
}

export default App