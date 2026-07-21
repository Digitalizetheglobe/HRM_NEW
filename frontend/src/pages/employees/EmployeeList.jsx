import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import api from '../../lib/axios';

export default function EmployeeList() {
  const [employees, setEmployees] = useState([]);
  const [activeCount, setActiveCount] = useState(0);
  const [inactiveCount, setInactiveCount] = useState(0);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState('active');
  const [search, setSearch] = useState('');

  useEffect(() => { fetchEmployees(); }, []);

  const fetchEmployees = async () => {
    try {
      const response = await api.get('/api/employees');
      setEmployees(response.data.employees);
      setActiveCount(response.data.activeCount);
      setInactiveCount(response.data.inactiveCount);
    } catch (error) {
      console.error('Failed to fetch employees', error);
    } finally {
      setLoading(false);
    }
  };

  const deleteEmployee = async (id, name) => {
    if (window.confirm(`Are you sure you want to delete ${name}?`)) {
      try {
        await api.delete(`/api/employees/${id}`);
        fetchEmployees();
      } catch (error) {
        console.error('Failed to delete employee', error);
      }
    }
  };

  const filteredEmployees = employees.filter(emp => {
    const isActive = emp.is_active === 1;
    const matchesTab = activeTab === 'active' ? isActive : !isActive;
    if (!matchesTab) return false;
    if (!search) return true;
    const term = search.toLowerCase();
    const branch = emp.employee?.branch?.name || '';
    const dept = emp.employee?.department?.name || '';
    const desig = emp.employee?.designation?.name || '';
    const uid = emp.employee?.employee_uid || '';
    return emp.name.toLowerCase().includes(term) || emp.email.toLowerCase().includes(term) || branch.toLowerCase().includes(term) || dept.toLowerCase().includes(term) || desig.toLowerCase().includes(term) || uid.toLowerCase().includes(term);
  });

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-8 w-8 border-2 border-gray-200 border-t-[#299dc6]"></div>
      </div>
    );
  }

  return (
    <div className="space-y-5">

      {/* Page Header */}
      <div className="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
          <div className="flex items-center space-x-1.5 text-xs font-medium text-gray-400 mb-1">
            <Link to="/dashboard" className="hover:text-[#299dc6] transition-colors">Home</Link>
            <span>/</span>
            <span className="text-gray-600">Employee</span>
          </div>
          <h1 className="text-2xl font-bold text-gray-900">Manage Employees</h1>
        </div>
        <Link to="/employees/create" className="flex items-center space-x-2 px-4 py-2.5 bg-[#299dc6] hover:bg-[#2389ae] text-white rounded-lg font-semibold text-sm shadow-sm transition-colors cursor-pointer">
          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4"></path></svg>
          <span>Add Employee</span>
        </Link>
      </div>

      {/* Main Card */}
      <div className="bg-white rounded-xl border border-gray-200/80 overflow-hidden">

        {/* Tabs */}
        <div className="flex border-b border-gray-100 px-1">
          <button
            onClick={() => setActiveTab('active')}
            className={`flex items-center space-x-2 px-4 py-3.5 border-b-2 text-sm font-medium transition-colors cursor-pointer ${activeTab === 'active' ? 'border-[#299dc6] text-[#299dc6]' : 'border-transparent text-gray-400 hover:text-gray-600'}`}
          >
            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Active</span>
            <span className="bg-gray-100 text-gray-500 text-xs font-semibold px-1.5 py-0.5 rounded-full">{activeCount}</span>
          </button>
          <button
            onClick={() => setActiveTab('inactive')}
            className={`flex items-center space-x-2 px-4 py-3.5 border-b-2 text-sm font-medium transition-colors cursor-pointer ${activeTab === 'inactive' ? 'border-red-400 text-red-500' : 'border-transparent text-gray-400 hover:text-gray-600'}`}
          >
            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Inactive</span>
            <span className="bg-gray-100 text-gray-500 text-xs font-semibold px-1.5 py-0.5 rounded-full">{inactiveCount}</span>
          </button>
        </div>

        {/* Search */}
        <div className="p-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-3">
          <div className="text-sm text-gray-500 font-medium">
            {filteredEmployees.length} employee{filteredEmployees.length !== 1 ? 's' : ''} found
          </div>
          <div className="relative w-full sm:w-64">
            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg className="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input
              type="text"
              placeholder="Search employees..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="w-full bg-gray-50 border border-gray-200 rounded-lg pl-10 pr-4 py-2 text-sm text-gray-700 placeholder:text-gray-400"
            />
          </div>
        </div>

        {/* Table */}
        <div className="overflow-x-auto">
          <table className="w-full text-left text-sm whitespace-nowrap">
            <thead className="bg-gray-50/80 text-gray-500 font-semibold uppercase tracking-wider text-[11px]">
              <tr>
                <th className="px-5 py-3 border-b border-gray-100">Employee ID</th>
                <th className="px-5 py-3 border-b border-gray-100">Name</th>
                <th className="px-5 py-3 border-b border-gray-100">Email</th>
                <th className="px-5 py-3 border-b border-gray-100">Branch</th>
                <th className="px-5 py-3 border-b border-gray-100">Department</th>
                <th className="px-5 py-3 border-b border-gray-100">Designation</th>
                <th className="px-5 py-3 border-b border-gray-100">Joining Date</th>
                <th className="px-5 py-3 border-b border-gray-100 text-right">Action</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-50">
              {filteredEmployees.length > 0 ? (
                filteredEmployees.map((user) => {
                  const emp = user.employee || {};
                  const branchName = emp.branch?.name || '—';
                  const deptName = emp.department?.name || '—';
                  const desigName = emp.designation?.name || '—';
                  const joiningDate = emp.joining_date ? new Date(emp.joining_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—';
                  const uid = emp.employee_uid || `#DTG${String(user.id).padStart(3, '0')}`;

                  return (
                    <tr key={user.id} className="hover:bg-gray-50/50 transition-colors">
                      <td className="px-5 py-3.5 font-mono text-[#299dc6] text-xs font-semibold">{uid}</td>
                      <td className="px-5 py-3.5">
                        <div className="flex items-center space-x-3">
                          {user.avatar ? (
                            <img src={`http://localhost:8000/storage/${user.avatar}`} alt="" className="w-8 h-8 rounded-full object-cover" />
                          ) : (
                            <div className="w-8 h-8 rounded-full bg-[#299dc6]/10 flex items-center justify-center text-[#299dc6] font-semibold text-xs">
                              {user.name.charAt(0).toUpperCase()}
                            </div>
                          )}
                          <span className="font-medium text-gray-800">{user.name}</span>
                        </div>
                      </td>
                      <td className="px-5 py-3.5 text-gray-500">{user.email}</td>
                      <td className="px-5 py-3.5 text-gray-600">{branchName}</td>
                      <td className="px-5 py-3.5">
                        <span className="bg-gray-100 text-gray-600 text-xs font-medium px-2 py-1 rounded-md">{deptName}</span>
                      </td>
                      <td className="px-5 py-3.5 text-gray-600">{desigName}</td>
                      <td className="px-5 py-3.5 text-gray-400 font-mono text-xs">{joiningDate}</td>
                      <td className="px-5 py-3.5 text-right">
                        <div className="flex items-center justify-end space-x-1">
                          <Link to={`/employees/${user.id}`} className="p-1.5 text-gray-400 hover:text-[#299dc6] hover:bg-[#299dc6]/8 rounded-md transition-colors cursor-pointer" title="View">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                          </Link>
                          <Link to={`/employees/${user.id}/edit`} className="p-1.5 text-gray-400 hover:text-amber-500 hover:bg-amber-50 rounded-md transition-colors cursor-pointer" title="Edit">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                          </Link>
                          <button onClick={() => deleteEmployee(user.id, user.name)} className="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-md transition-colors cursor-pointer" title="Delete">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                          </button>
                        </div>
                      </td>
                    </tr>
                  );
                })
              ) : (
                <tr>
                  <td colSpan="8" className="px-5 py-12 text-center text-gray-400">
                    <svg className="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <p className="font-medium">No employees found</p>
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
