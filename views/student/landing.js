import React, { useState } from 'react';
import { BookOpen, LogIn, UserPlus, Link2 } from 'lucide-react';

export default function MathQuizLanding() {
  const [showLogin, setShowLogin] = useState(false);
  const [showRegister, setShowRegister] = useState(false);
  const [showGuestQuiz, setShowGuestQuiz] = useState(false);

  const handleSubmit = (type) => {
    alert(`This is a demo. In the real app, this would submit the ${type} form.`);
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-purple-400 via-pink-300 to-blue-300 relative overflow-hidden">
      {/* Floating Math Symbols Animation */}
      <div className="absolute inset-0 pointer-events-none">
        {['+', '−', '×', '÷', '=', '√', 'π', '∞'].map((symbol, i) => (
          <div
            key={i}
            className="absolute text-white text-4xl opacity-20"
            style={{
              left: `${Math.random() * 100}%`,
              top: `${Math.random() * 100}%`,
              animation: `float ${3 + Math.random() * 2}s ease-in-out infinite`,
              animationDelay: `${i * 0.5}s`
            }}
          >
            {symbol}
          </div>
        ))}
      </div>

      <style>{`
        @keyframes float {
          0%, 100% { transform: translateY(0px) rotate(0deg); }
          50% { transform: translateY(-20px) rotate(10deg); }
        }
        @keyframes fadeIn {
          from { opacity: 0; transform: translateY(-10px); }
          to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
          animation: fadeIn 0.3s ease-out;
        }
      `}</style>

      <div className="container mx-auto px-4 py-8 relative z-10">
        {/* Header */}
        <div className="text-center mb-8">
          <div className="inline-flex items-center gap-2 bg-white bg-opacity-90 px-6 py-3 rounded-full shadow-lg mb-4">
            <BookOpen className="text-purple-600" size={32} />
            <h1 className="text-3xl font-bold text-purple-600">
              Math Adventure Quiz
            </h1>
          </div>
        </div>

        {/* Main Content */}
        <div className="max-w-6xl mx-auto">
          <div className="grid md:grid-cols-2 gap-8 items-center">
            {/* Teacher Character Section */}
            <div className="bg-white bg-opacity-95 rounded-3xl shadow-2xl p-8 transform hover:scale-105 transition-all duration-300">
              <div className="text-center">
                <div className="w-48 h-48 mx-auto mb-6 bg-gradient-to-br from-pink-200 to-purple-200 rounded-full flex items-center justify-center shadow-xl">
                  <div className="text-6xl">👩‍🏫</div>
                </div>
                <h2 className="text-3xl font-bold text-gray-800 mb-2">Teacher Lilibeth Bordan</h2>
                <div className="bg-purple-100 rounded-2xl p-6 mb-6">
                  <p className="text-lg text-gray-700 leading-relaxed">
                    "Welcome to Math Adventure! 🎉 I'm so excited to have you here. Let's make learning math fun and exciting together!"
                  </p>
                </div>
                <div className="flex flex-wrap gap-3 justify-center">
                  <div className="bg-yellow-100 px-4 py-2 rounded-full">
                    <span className="text-sm font-semibold text-yellow-800">⭐ Fun Quizzes</span>
                  </div>
                  <div className="bg-green-100 px-4 py-2 rounded-full">
                    <span className="text-sm font-semibold text-green-800">🏆 Track Progress</span>
                  </div>
                  <div className="bg-blue-100 px-4 py-2 rounded-full">
                    <span className="text-sm font-semibold text-blue-800">🎮 Game Style</span>
                  </div>
                </div>
              </div>
            </div>

            {/* Action Buttons Section */}
            <div className="space-y-4">
              {/* Login Card */}
              <div className="bg-white bg-opacity-95 rounded-2xl shadow-xl p-6 hover:shadow-2xl transition-all duration-300 cursor-pointer border-4 border-transparent hover:border-purple-400"
                   onClick={() => setShowLogin(!showLogin)}>
                <div className="flex items-center gap-4 mb-4">
                  <div className="bg-purple-100 p-4 rounded-xl">
                    <LogIn className="text-purple-600" size={32} />
                  </div>
                  <div>
                    <h3 className="text-2xl font-bold text-gray-800">Student Login</h3>
                    <p className="text-gray-600">Already have an account?</p>
                  </div>
                </div>
                {showLogin && (
                  <div className="space-y-3 mt-4 animate-fadeIn">
                    <input
                      type="text"
                      placeholder="Enter your name"
                      className="w-full px-4 py-3 border-2 border-purple-200 rounded-xl focus:border-purple-500 focus:outline-none"
                    />
                    <input
                      type="password"
                      placeholder="Enter your password"
                      className="w-full px-4 py-3 border-2 border-purple-200 rounded-xl focus:border-purple-500 focus:outline-none"
                    />
                    <button 
                      onClick={() => handleSubmit('login')}
                      className="w-full bg-gradient-to-r from-purple-500 to-pink-500 text-white py-3 rounded-xl font-bold hover:from-purple-600 hover:to-pink-600 transition-all duration-300 shadow-lg">
                      Login Now
                    </button>
                  </div>
                )}
              </div>

              {/* Register Card */}
              <div className="bg-white bg-opacity-95 rounded-2xl shadow-xl p-6 hover:shadow-2xl transition-all duration-300 cursor-pointer border-4 border-transparent hover:border-green-400"
                   onClick={() => setShowRegister(!showRegister)}>
                <div className="flex items-center gap-4 mb-4">
                  <div className="bg-green-100 p-4 rounded-xl">
                    <UserPlus className="text-green-600" size={32} />
                  </div>
                  <div>
                    <h3 className="text-2xl font-bold text-gray-800">Create Account</h3>
                    <p className="text-gray-600">New student? Register here!</p>
                  </div>
                </div>
                {showRegister && (
                  <div className="space-y-3 mt-4 animate-fadeIn">
                    <input
                      type="text"
                      placeholder="Full Name"
                      className="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:border-green-500 focus:outline-none"
                    />
                    <div className="grid grid-cols-2 gap-3">
                      <select className="px-4 py-3 border-2 border-green-200 rounded-xl focus:border-green-500 focus:outline-none">
                        <option>Select Grade</option>
                        <option>Grade 1</option>
                        <option>Grade 2</option>
                        <option>Grade 3</option>
                        <option>Grade 4</option>
                        <option>Grade 5</option>
                        <option>Grade 6</option>
                      </select>
                      <input
                        type="text"
                        placeholder="Section"
                        className="px-4 py-3 border-2 border-green-200 rounded-xl focus:border-green-500 focus:outline-none"
                      />
                    </div>
                    <input
                      type="password"
                      placeholder="Create Password"
                      className="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:border-green-500 focus:outline-none"
                    />
                    <button 
                      onClick={() => handleSubmit('register')}
                      className="w-full bg-gradient-to-r from-green-500 to-teal-500 text-white py-3 rounded-xl font-bold hover:from-green-600 hover:to-teal-600 transition-all duration-300 shadow-lg">
                      Register Now
                    </button>
                  </div>
                )}
              </div>

              {/* Guest Quiz Link Card */}
              <div className="bg-white bg-opacity-95 rounded-2xl shadow-xl p-6 hover:shadow-2xl transition-all duration-300 cursor-pointer border-4 border-transparent hover:border-blue-400"
                   onClick={() => setShowGuestQuiz(!showGuestQuiz)}>
                <div className="flex items-center gap-4 mb-4">
                  <div className="bg-blue-100 p-4 rounded-xl">
                    <Link2 className="text-blue-600" size={32} />
                  </div>
                  <div>
                    <h3 className="text-2xl font-bold text-gray-800">Join with Link</h3>
                    <p className="text-gray-600">Have a quiz code?</p>
                  </div>
                </div>
                {showGuestQuiz && (
                  <div className="space-y-3 mt-4 animate-fadeIn">
                    <input
                      type="text"
                      placeholder="Enter Quiz Code (e.g., ABC12345)"
                      className="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:border-blue-500 focus:outline-none uppercase"
                    />
                    <button 
                      onClick={() => handleSubmit('guest')}
                      className="w-full bg-gradient-to-r from-blue-500 to-cyan-500 text-white py-3 rounded-xl font-bold hover:from-blue-600 hover:to-cyan-600 transition-all duration-300 shadow-lg">
                      Join Quiz
                    </button>
                  </div>
                )}
              </div>
            </div>
          </div>
        </div>

        {/* Footer */}
        <div className="text-center mt-12">
          <p className="text-white text-lg font-semibold" style={{textShadow: '2px 2px 4px rgba(0,0,0,0.3)'}}>
            Let's make math fun! 🚀 Start your adventure now!
          </p>
        </div>
      </div>
    </div>
  );
}