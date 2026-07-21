import React from 'react';

export default function Dashboard() {
  const today = new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

  const stats = [
    { label: 'Total Employees', value: '1', delta: '+1 this month', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', color: '#299dc6', trend: 'up' },
    { label: 'Departments', value: '0', delta: 'No change', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', color: '#299dc6', trend: 'neutral' },
    { label: 'Total Leaves', value: '0', delta: 'All on duty', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', color: '#62975f', trend: 'down' },
    { label: 'Holidays', value: '0', delta: 'None scheduled', icon: 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z', color: '#299dc6', trend: 'neutral' },
    { label: 'Total Projects', value: '0', delta: 'Start a project', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', color: '#62975f', trend: 'up' },
    { label: 'Total Tickets', value: '0', delta: 'All resolved', icon: 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z', color: '#299dc6', trend: 'neutral' },
  ];

  return (
    <div className="space-y-6">

      {/* Page Title */}
      <div className="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Dashboard</h1>
          <p className="text-sm text-gray-500 mt-0.5">{today} · Welcome back!</p>
        </div>
        <button className="flex items-center space-x-2 px-4 py-2.5 text-white rounded-lg font-semibold text-sm shadow-sm transition-colors cursor-pointer" style={{ backgroundColor: '#299dc6' }}
          onMouseEnter={(e) => e.currentTarget.style.backgroundColor = '#2389ae'}
          onMouseLeave={(e) => e.currentTarget.style.backgroundColor = '#299dc6'}
        >
          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
          <span>Export Report</span>
        </button>
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        {stats.map((stat, i) => (
          <div key={i} className="rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow" style={{ backgroundColor: '#ffffff' }}>
            <div className="flex justify-between items-start mb-4">
              <div className="w-10 h-10 rounded-lg flex items-center justify-center" style={{ backgroundColor: stat.color + '14' }}>
                <svg className="w-5 h-5" style={{ color: stat.color }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" d={stat.icon}></path>
                </svg>
              </div>
              {stat.trend === 'up' && (
                <span className="flex items-center text-[11px] font-semibold px-2 py-1 rounded-md" style={{ color: '#62975f', backgroundColor: 'rgba(98,151,95,0.08)' }}>
                  <svg className="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                  Up
                </span>
              )}
              {stat.trend === 'down' && (
                <span className="flex items-center text-[11px] font-semibold px-2 py-1 rounded-md" style={{ color: '#62975f', backgroundColor: 'rgba(98,151,95,0.08)' }}>
                  <svg className="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7"></path></svg>
                  Good
                </span>
              )}
              {stat.trend === 'neutral' && (
                <span className="flex items-center text-[11px] font-semibold text-gray-400 px-2 py-1 rounded-md" style={{ backgroundColor: '#f3f4f6' }}>
                  <svg className="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M20 12H4"></path></svg>
                  Flat
                </span>
              )}
            </div>
            <p className="text-sm text-gray-500 font-medium mb-0.5">{stat.label}</p>
            <h3 className="text-2xl font-bold text-gray-900">{stat.value}</h3>
            <p className="text-xs text-gray-400 mt-1">{stat.delta}</p>
          </div>
        ))}
      </div>

      {/* Attendance */}
      <div className="rounded-xl border border-gray-200 overflow-hidden" style={{ backgroundColor: '#ffffff' }}>
        <div className="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div className="flex items-center gap-2.5">
            <div className="w-8 h-8 rounded-lg flex items-center justify-center" style={{ backgroundColor: 'rgba(41,157,198,0.1)' }}>
              <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style={{ color: '#299dc6' }}><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"></path></svg>
            </div>
            <h2 className="text-[15px] font-semibold text-gray-800">Today's Attendance</h2>
          </div>
          <div className="flex items-center gap-5">
            <div className="flex items-center gap-1.5">
              <span className="w-2 h-2 rounded-full" style={{ backgroundColor: '#62975f' }}></span>
              <span className="text-xs font-medium text-gray-500">Clocked In: <strong style={{ color: '#62975f' }}>0</strong></span>
            </div>
            <div className="flex items-center gap-1.5">
              <span className="w-2 h-2 bg-red-400 rounded-full"></span>
              <span className="text-xs font-medium text-gray-500">Absent: <strong className="text-red-500">0</strong></span>
            </div>
            <button className="text-xs font-semibold px-3 py-1.5 rounded-md transition-colors cursor-pointer" style={{ color: '#299dc6', backgroundColor: 'rgba(41,157,198,0.08)' }}
              onMouseEnter={(e) => e.currentTarget.style.backgroundColor = 'rgba(41,157,198,0.15)'}
              onMouseLeave={(e) => e.currentTarget.style.backgroundColor = 'rgba(41,157,198,0.08)'}
            >
              View All
            </button>
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-gray-100">
          <div className="p-5">
            <div className="flex items-center gap-2 mb-3">
              <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style={{ color: '#62975f' }}><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              <h3 className="text-xs font-semibold uppercase tracking-wider" style={{ color: '#62975f' }}>Clock-In Employees</h3>
            </div>
            <div className="rounded-lg border border-gray-100 overflow-hidden">
              <table className="w-full text-left text-sm">
                <thead style={{ backgroundColor: '#f9fafb' }}>
                  <tr>
                    <th className="px-4 py-2.5 font-semibold text-gray-500 text-xs uppercase tracking-wider">Employee</th>
                    <th className="px-4 py-2.5 font-semibold text-gray-500 text-xs uppercase tracking-wider">Clock-In Time</th>
                  </tr>
                </thead>
                <tbody>
                  <tr><td colSpan="2" className="px-4 py-8 text-center text-gray-400 text-sm">No clock-ins recorded today</td></tr>
                </tbody>
              </table>
            </div>
          </div>
          <div className="p-5">
            <div className="flex items-center gap-2 mb-3">
              <svg className="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              <h3 className="text-xs font-semibold text-red-400 uppercase tracking-wider">Not Clocked In</h3>
            </div>
            <div className="rounded-lg border border-gray-100 overflow-hidden">
              <table className="w-full text-left text-sm">
                <thead style={{ backgroundColor: '#f9fafb' }}>
                  <tr>
                    <th className="px-4 py-2.5 font-semibold text-gray-500 text-xs uppercase tracking-wider">Employee</th>
                    <th className="px-4 py-2.5 font-semibold text-gray-500 text-xs uppercase tracking-wider">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr><td colSpan="2" className="px-4 py-8 text-center text-gray-400 text-sm">All employees accounted for</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
