import { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import api from '../../lib/axios';

export default function EmployeeShow() {
  const { id } = useParams();
  const [employee, setEmployee] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => { fetchEmployee(); }, [id]);

  const fetchEmployee = async () => {
    try {
      const response = await api.get(`/api/employees/${id}`);
      setEmployee(response.data.employee);
    } catch (err) { setError('Failed to load employee details.'); }
    finally { setLoading(false); }
  };

  if (loading) return <div className="flex items-center justify-center h-64"><div className="animate-spin rounded-full h-8 w-8 border-2 border-gray-200 border-t-[#299dc6]"></div></div>;

  if (error || !employee) {
    return (
      <div className="flex flex-col items-center justify-center h-64 text-gray-400 space-y-3">
        <svg className="w-10 h-10 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        <p className="text-sm font-medium">{error || 'Employee not found.'}</p>
        <Link to="/employees" className="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50">Go Back</Link>
      </div>
    );
  }

  const emp = employee.employee || {};
  const branchName = emp.branch?.name || '—';
  const deptName = emp.department?.name || '—';
  const desigName = emp.designation?.name || '—';
  const uid = emp.employee_uid || `#DTG${String(employee.id).padStart(3, '0')}`;
  const joiningDate = emp.joining_date ? new Date(emp.joining_date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) : '—';
  const dob = emp.dob ? new Date(emp.dob).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) : '—';

  const InfoItem = ({ label, value, mono }) => (
    <div>
      <p className="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-0.5">{label}</p>
      <p className={`text-sm font-medium text-gray-700 ${mono ? 'font-mono' : ''}`}>{value || '—'}</p>
    </div>
  );

  return (
    <div className="space-y-5 max-w-5xl mx-auto">

      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
          <div className="flex items-center space-x-1.5 text-xs font-medium text-gray-400 mb-1">
            <Link to="/dashboard" className="hover:text-[#299dc6]">Home</Link><span>/</span>
            <Link to="/employees" className="hover:text-[#299dc6]">Employee</Link><span>/</span>
            <span className="text-gray-600">Profile</span>
          </div>
          <h1 className="text-2xl font-bold text-gray-900">Employee Profile</h1>
        </div>
        <div className="flex items-center gap-2">
          <Link to="/employees" className="px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 rounded-lg font-medium text-sm transition-colors flex items-center gap-1.5 cursor-pointer">
            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back
          </Link>
          <Link to={`/employees/${employee.id}/edit`} className="px-4 py-2 bg-[#299dc6] hover:bg-[#2389ae] text-white rounded-lg font-semibold text-sm shadow-sm transition-colors flex items-center gap-1.5 cursor-pointer">
            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            Edit
          </Link>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {/* Sidebar Card */}
        <div className="lg:col-span-1">
          <div className="bg-white rounded-xl border border-gray-200/80 p-6 flex flex-col items-center text-center">
            {/* Avatar */}
            <div className="mb-4 relative">
              {employee.avatar ? (
                <img src={`http://localhost:8000/storage/${employee.avatar}`} alt="" className="w-24 h-24 rounded-2xl object-cover shadow-sm" />
              ) : (
                <div className="w-24 h-24 rounded-2xl bg-[#299dc6]/10 flex items-center justify-center text-[#299dc6] font-bold text-3xl shadow-sm">
                  {employee.name.charAt(0).toUpperCase()}
                </div>
              )}
              <div className={`absolute -bottom-1.5 -right-1.5 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-md border-2 border-white ${employee.is_active ? 'bg-[#62975f] text-white' : 'bg-red-400 text-white'}`}>
                {employee.is_active ? 'Active' : 'Inactive'}
              </div>
            </div>

            <h2 className="text-lg font-bold text-gray-900">{employee.name}</h2>
            <p className="text-[#299dc6] font-mono text-sm font-semibold mb-0.5">{uid}</p>
            <p className="text-gray-500 text-sm flex items-center gap-1.5 mb-5">
              <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
              {desigName}
            </p>

            <div className="w-full h-px bg-gray-100 mb-5"></div>

            {/* Contact Info */}
            <div className="w-full text-left space-y-4">
              <div className="flex items-start gap-3">
                <div className="w-8 h-8 rounded-lg bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 shrink-0">
                  <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div className="min-w-0">
                  <p className="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Email</p>
                  <p className="text-sm font-medium text-gray-700 truncate" title={employee.email}>{employee.email}</p>
                </div>
              </div>
              <div className="flex items-start gap-3">
                <div className="w-8 h-8 rounded-lg bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 shrink-0">
                  <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                </div>
                <div>
                  <p className="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Phone</p>
                  <p className="text-sm font-medium text-gray-700">{emp.phone || '—'}</p>
                </div>
              </div>
              <div className="flex items-start gap-3">
                <div className="w-8 h-8 rounded-lg bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 shrink-0">
                  <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <div>
                  <p className="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Address</p>
                  <p className="text-sm font-medium text-gray-700 leading-relaxed">{emp.address || '—'}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Main Content */}
        <div className="lg:col-span-2 space-y-5">

          {/* Company */}
          <div className="bg-white rounded-xl border border-gray-200/80 p-6 border-l-4 border-l-[#299dc6]">
            <h2 className="text-base font-bold text-gray-800 mb-5 flex items-center gap-2">
              <svg className="w-5 h-5 text-[#299dc6]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
              Company Details
            </h2>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-y-5 gap-x-8">
              <InfoItem label="Branch" value={branchName} />
              <InfoItem label="Department" value={deptName} />
              <InfoItem label="Date of Joining" value={joiningDate} />
              <InfoItem label="Date of Birth" value={dob} />
              <InfoItem label="Gender" value={emp.gender ? emp.gender.charAt(0).toUpperCase() + emp.gender.slice(1) : '—'} />
              <InfoItem label="Salary" value={`₹${emp.basic_salary || '0'} / ${emp.salary_type || 'Month'}`} />
            </div>
          </div>

          {/* Bank */}
          <div className="bg-white rounded-xl border border-gray-200/80 p-6 border-l-4 border-l-[#62975f]">
            <h2 className="text-base font-bold text-gray-800 mb-5 flex items-center gap-2">
              <svg className="w-5 h-5 text-[#62975f]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
              Bank Account Info
            </h2>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-y-5 gap-x-8">
              <InfoItem label="Account Holder" value={emp.account_holder_name} />
              <InfoItem label="Account Number" value={emp.account_number} mono />
              <InfoItem label="Bank Name" value={emp.bank_name} />
              <InfoItem label="IFSC Code" value={emp.ifsc_code} mono />
              <InfoItem label="Bank Branch" value={emp.bank_branch} />
              <InfoItem label="PAN Number" value={emp.pan_number} mono />
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
