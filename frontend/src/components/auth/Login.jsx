import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import api from '../../lib/axios'

export default function Login() {
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [showPassword, setShowPassword] = useState(false)
  const [remember, setRemember] = useState(false)
  const [error, setError] = useState('')
  const [isLoading, setIsLoading] = useState(false)
  const navigate = useNavigate()

  const handleSubmit = async (e) => {
    e.preventDefault()
    setError('')
    setIsLoading(true)
    try {
      await api.get('/sanctum/csrf-cookie')
      await api.post('/api/login', { email, password, remember })
      navigate('/dashboard')
    } catch (err) {
      if (err.response?.data?.errors) setError(Object.values(err.response.data.errors)[0][0])
      else if (err.response?.data?.message) setError(err.response.data.message)
      else setError('An error occurred during login. Please try again.')
    } finally { setIsLoading(false) }
  }

  return (
    <div className="min-h-screen flex items-center justify-center relative overflow-hidden" style={{ backgroundColor: '#f8f9fb', fontFamily: "'Inter', sans-serif" }}>

      {/* Subtle background circles */}
      <div className="absolute top-0 right-0 w-[600px] h-[600px] rounded-full -translate-y-1/2 translate-x-1/3" style={{ backgroundColor: 'rgba(41,157,198,0.05)' }}></div>
      <div className="absolute bottom-0 left-0 w-[400px] h-[400px] rounded-full translate-y-1/3 -translate-x-1/4" style={{ backgroundColor: 'rgba(98,151,95,0.05)' }}></div>

      <div className="relative z-10 w-full max-w-[420px] mx-4">
        <div className="rounded-2xl shadow-sm border border-gray-200 p-8" style={{ backgroundColor: '#ffffff' }}>

          {/* Brand */}
          <div className="flex items-center space-x-3 mb-8">
            <div className="w-10 h-10 rounded-xl flex items-center justify-center text-white shadow-sm" style={{ backgroundColor: '#299dc6' }}>
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
              <h2 className="text-lg font-bold text-gray-900 tracking-tight">DTGHRM</h2>
              <p className="text-[10px] text-gray-400 font-medium tracking-wider uppercase">The Globe — HR Management</p>
            </div>
          </div>

          <div className="mb-7">
            <h1 className="text-2xl font-bold text-gray-900 mb-1">Welcome Back</h1>
            <p className="text-sm text-gray-500">Sign in to your account to continue</p>
          </div>

          {error && (
            <div className="mb-5 p-3.5 bg-red-50 border border-red-200 rounded-lg flex items-start space-x-2.5 text-red-600 text-sm">
              <svg className="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              <span className="font-medium">{error}</span>
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-5">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1.5" htmlFor="email">Email Address</label>
              <input id="email" type="email" value={email} onChange={(e) => setEmail(e.target.value)} placeholder="name@company.com" required
                className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-gray-800 text-sm placeholder:text-gray-400" style={{ backgroundColor: '#ffffff' }} />
            </div>

            <div>
              <div className="flex justify-between items-center mb-1.5">
                <label className="text-sm font-medium text-gray-700" htmlFor="password">Password</label>
                <a href="#" className="text-xs font-medium" style={{ color: '#299dc6' }}>Forgot password?</a>
              </div>
              <div className="relative">
                <input id="password" type={showPassword ? 'text' : 'password'} value={password} onChange={(e) => setPassword(e.target.value)} placeholder="••••••••" required
                  className="w-full px-3.5 py-2.5 pr-11 border border-gray-300 rounded-lg text-gray-800 text-sm placeholder:text-gray-400" style={{ backgroundColor: '#ffffff' }} />
                <button type="button" onClick={() => setShowPassword(!showPassword)} className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 cursor-pointer">
                  {showPassword ? (
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                  ) : (
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                  )}
                </button>
              </div>
            </div>

            <div className="flex items-center">
              <input id="remember" type="checkbox" checked={remember} onChange={(e) => setRemember(e.target.checked)} className="w-4 h-4 border-gray-300 rounded" />
              <label htmlFor="remember" className="ml-2 text-sm text-gray-500 select-none cursor-pointer">Remember me</label>
            </div>

            <button type="submit" disabled={isLoading}
              className="w-full py-2.5 px-4 text-white rounded-lg font-semibold text-sm shadow-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
              style={{ backgroundColor: '#299dc6' }}
              onMouseEnter={(e) => { if (!isLoading) e.currentTarget.style.backgroundColor = '#2389ae' }}
              onMouseLeave={(e) => { if (!isLoading) e.currentTarget.style.backgroundColor = '#299dc6' }}
            >
              {isLoading ? (
                <span className="flex items-center justify-center">
                  <svg className="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Signing in...
                </span>
              ) : 'Sign In'}
            </button>
          </form>

          <div className="mt-7 text-center text-sm text-gray-500">
            New to DTGHRM? <a href="#" className="font-medium" style={{ color: '#299dc6' }}>Create an account</a>
          </div>
        </div>

        <p className="text-center text-xs text-gray-400 mt-6">© 2026 Digitalize The Globe. All rights reserved.</p>
      </div>
    </div>
  )
}
