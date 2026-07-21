import { useState, useEffect } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import api from '../../lib/axios';

export default function EmployeeCreate() {
  const navigate = useNavigate();
  const [step, setStep] = useState(1);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState(null);
  const [branches, setBranches] = useState([]);
  const [departments, setDepartments] = useState([]);
  const [designations, setDesignations] = useState([]);
  const [nextUid, setNextUid] = useState('');

  const [formData, setFormData] = useState({
    name: '', email: '', password: '', phone: '', dob: '2000-01-01', gender: 'male', address: '',
    branch_id: '', department_id: '', designation_id: '',
    salary_type: 'Monthly', basic_salary: '0', joining_date: new Date().toISOString().split('T')[0], is_active: true,
    account_holder_name: '', account_number: '', bank_name: '', bank_branch: '', ifsc_code: '', pan_number: ''
  });

  const [files, setFiles] = useState({
    doc_passport_photo: null, doc_aadhar_card: null, doc_pan_card: null,
    doc_marksheet_10th: null, doc_marksheet_12th: null, doc_degree_certificate: null,
    doc_experience_letter: null, doc_offer_letter: null
  });

  useEffect(() => { fetchFormData(); }, []);

  const fetchFormData = async () => {
    try {
      const response = await api.get('/api/employees/create-data');
      setBranches(response.data.branches);
      setDepartments(response.data.departments);
      setDesignations(response.data.designations);
      setNextUid(response.data.nextUid);
    } catch (err) { setError('Failed to load form data.'); }
    finally { setLoading(false); }
  };

  const handleInputChange = (e) => {
    const { name, value, type, checked } = e.target;
    setFormData(prev => ({ ...prev, [name]: type === 'checkbox' ? checked : value }));
  };

  const handleFileChange = (e) => {
    const { name, files: f } = e.target;
    if (f.length > 0) setFiles(prev => ({ ...prev, [name]: f[0] }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSaving(true); setError(null);
    const submitData = new FormData();
    Object.keys(formData).forEach(key => submitData.append(key, formData[key]));
    Object.keys(files).forEach(key => { if (files[key]) submitData.append(key, files[key]); });
    try {
      await api.post('/api/employees', submitData, { headers: { 'Content-Type': 'multipart/form-data' } });
      navigate('/employees');
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to create employee.');
    } finally { setSaving(false); }
  };

  const nextStep = () => setStep(prev => Math.min(prev + 1, 3));
  const prevStep = () => setStep(prev => Math.max(prev - 1, 1));

  if (loading) return <div className="flex items-center justify-center h-64"><div className="animate-spin rounded-full h-8 w-8 border-2 border-gray-200 border-t-[#299dc6]"></div></div>;

  const filteredDepts = departments.filter(d => d.branch_id == formData.branch_id);
  const filteredDesigs = designations.filter(d => d.department_id == formData.department_id);

  const inputCls = "w-full bg-white border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-800 placeholder:text-gray-400";
  const labelCls = "block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5";

  return (
    <div className="space-y-5 max-w-4xl mx-auto">
      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
          <div className="flex items-center space-x-1.5 text-xs font-medium text-gray-400 mb-1">
            <Link to="/dashboard" className="hover:text-[#299dc6]">Home</Link><span>/</span>
            <Link to="/employees" className="hover:text-[#299dc6]">Employee</Link><span>/</span>
            <span className="text-gray-600">Add</span>
          </div>
          <h1 className="text-2xl font-bold text-gray-900">Create Employee</h1>
        </div>
        <Link to="/employees" className="px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 rounded-lg font-medium text-sm transition-colors">Back to List</Link>
      </div>

      {error && (
        <div className="p-3.5 bg-red-50 border border-red-200 rounded-lg text-red-600 text-sm font-medium flex items-center gap-2">
          <svg className="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
          {error}
        </div>
      )}

      {/* Steps */}
      <div className="flex items-center justify-between relative mb-2">
        <div className="absolute left-0 top-1/2 -translate-y-1/2 w-full h-0.5 bg-gray-200 -z-10 rounded-full"></div>
        <div className="absolute left-0 top-1/2 -translate-y-1/2 h-0.5 bg-[#299dc6] -z-10 rounded-full transition-all duration-300" style={{ width: `${((step - 1) / 2) * 100}%` }}></div>
        {[{ n: 1, l: 'Personal Info' }, { n: 2, l: 'Documents' }, { n: 3, l: 'Review' }].map(s => (
          <div key={s.n} className="flex flex-col items-center gap-1.5">
            <div className={`w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold transition-colors ${step >= s.n ? 'bg-[#299dc6] text-white' : 'bg-white border-2 border-gray-200 text-gray-400'}`}>
              {step > s.n ? <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M5 13l4 4L19 7"></path></svg> : s.n}
            </div>
            <span className={`text-[10px] font-semibold uppercase tracking-wider hidden sm:block ${step >= s.n ? 'text-[#299dc6]' : 'text-gray-400'}`}>{s.l}</span>
          </div>
        ))}
      </div>

      <form onSubmit={handleSubmit} className="bg-white rounded-xl border border-gray-200/80 p-6 sm:p-8">

        {step === 1 && (
          <div className="space-y-7">
            <div>
              <h2 className="text-base font-bold text-gray-800 border-b border-gray-100 pb-3 mb-5">Personal Details</h2>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label className={labelCls}>Name *</label><input type="text" name="name" value={formData.name} onChange={handleInputChange} required className={inputCls} /></div>
                <div><label className={labelCls}>Phone *</label><input type="tel" name="phone" value={formData.phone} onChange={handleInputChange} required className={inputCls} /></div>
                <div><label className={labelCls}>Date of Birth *</label><input type="date" name="dob" value={formData.dob} onChange={handleInputChange} required className={inputCls} /></div>
                <div><label className={labelCls}>Gender *</label><select name="gender" value={formData.gender} onChange={handleInputChange} className={inputCls}><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select></div>
                <div><label className={labelCls}>Email *</label><input type="email" name="email" value={formData.email} onChange={handleInputChange} required className={inputCls} /></div>
                <div><label className={labelCls}>Password *</label><input type="password" name="password" value={formData.password} onChange={handleInputChange} required className={inputCls} /></div>
                <div className="md:col-span-2"><label className={labelCls}>Address *</label><textarea name="address" value={formData.address} onChange={handleInputChange} required rows="2" className={inputCls}></textarea></div>
              </div>
            </div>
            <div>
              <h2 className="text-base font-bold text-gray-800 border-b border-gray-100 pb-3 mb-5">Company Details</h2>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="md:col-span-2">
                  <label className={labelCls}>Employee ID</label>
                  <div className="px-3.5 py-2.5 bg-[#299dc6]/5 border border-[#299dc6]/20 rounded-lg text-[#299dc6] font-mono font-bold text-sm w-max">{nextUid}</div>
                </div>
                <div><label className={labelCls}>Branch *</label><select name="branch_id" value={formData.branch_id} onChange={handleInputChange} required className={inputCls}><option value="">Select Branch</option>{branches.map(b => <option key={b.id} value={b.id}>{b.name}</option>)}</select></div>
                <div><label className={labelCls}>Department *</label><select name="department_id" value={formData.department_id} onChange={handleInputChange} required className={inputCls}><option value="">Select Department</option>{filteredDepts.map(d => <option key={d.id} value={d.id}>{d.name}</option>)}</select></div>
                <div><label className={labelCls}>Designation *</label><select name="designation_id" value={formData.designation_id} onChange={handleInputChange} required className={inputCls}><option value="">Select Designation</option>{filteredDesigs.map(d => <option key={d.id} value={d.id}>{d.name}</option>)}</select></div>
                <div><label className={labelCls}>Joining Date *</label><input type="date" name="joining_date" value={formData.joining_date} onChange={handleInputChange} required className={inputCls} /></div>
              </div>
            </div>
          </div>
        )}

        {step === 2 && (
          <div className="space-y-7">
            <div>
              <h2 className="text-base font-bold text-gray-800 border-b border-gray-100 pb-3 mb-5">Bank Account Details</h2>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label className={labelCls}>Account Name</label><input type="text" name="account_holder_name" value={formData.account_holder_name} onChange={handleInputChange} className={inputCls} /></div>
                <div><label className={labelCls}>Account Number</label><input type="text" name="account_number" value={formData.account_number} onChange={handleInputChange} className={inputCls} /></div>
                <div><label className={labelCls}>Bank Name</label><input type="text" name="bank_name" value={formData.bank_name} onChange={handleInputChange} className={inputCls} /></div>
                <div><label className={labelCls}>IFSC Code</label><input type="text" name="ifsc_code" value={formData.ifsc_code} onChange={handleInputChange} className={`${inputCls} font-mono uppercase`} /></div>
              </div>
            </div>
            <div>
              <h2 className="text-base font-bold text-gray-800 border-b border-gray-100 pb-3 mb-5">Documents Upload</h2>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                {Object.keys(files).map(doc => (
                  <div key={doc} className="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <label className="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">{doc.replace('doc_', '').replace(/_/g, ' ')}</label>
                    <input type="file" name={doc} onChange={handleFileChange} className="text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-white file:text-[#299dc6] file:border file:border-gray-200 file:shadow-sm hover:file:bg-gray-50 file:cursor-pointer cursor-pointer" />
                  </div>
                ))}
              </div>
            </div>
          </div>
        )}

        {step === 3 && (
          <div className="space-y-5">
            <h2 className="text-base font-bold text-gray-800 border-b border-gray-100 pb-3 mb-5">Review Details</h2>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div className="p-5 bg-gray-50 rounded-lg border border-gray-100">
                <h3 className="text-[#299dc6] font-bold mb-3 uppercase text-xs tracking-wider">Personal</h3>
                <div className="grid grid-cols-2 gap-y-2.5 text-sm">
                  <div className="text-gray-400">Name:</div><div className="text-gray-800 font-medium">{formData.name}</div>
                  <div className="text-gray-400">Email:</div><div className="text-gray-800 font-medium">{formData.email}</div>
                  <div className="text-gray-400">Phone:</div><div className="text-gray-800 font-medium">{formData.phone}</div>
                </div>
              </div>
              <div className="p-5 bg-gray-50 rounded-lg border border-gray-100">
                <h3 className="text-[#299dc6] font-bold mb-3 uppercase text-xs tracking-wider">Company</h3>
                <div className="grid grid-cols-2 gap-y-2.5 text-sm">
                  <div className="text-gray-400">UID:</div><div className="text-gray-800 font-mono font-medium">{nextUid}</div>
                  <div className="text-gray-400">Joining:</div><div className="text-gray-800 font-medium">{formData.joining_date}</div>
                </div>
              </div>
            </div>
            <div className="bg-[#62975f]/8 border border-[#62975f]/20 rounded-lg p-4 flex gap-2.5 mt-4">
              <svg className="w-5 h-5 text-[#62975f] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              <p className="text-sm text-[#62975f] font-medium">Please verify all information before saving.</p>
            </div>
          </div>
        )}

        {/* Actions */}
        <div className="flex items-center justify-between mt-8 pt-5 border-t border-gray-100">
          <button type="button" onClick={prevStep} disabled={step === 1} className={`px-5 py-2.5 rounded-lg font-medium text-sm transition-colors cursor-pointer ${step === 1 ? 'opacity-0 pointer-events-none' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50'}`}>Previous</button>
          {step < 3 ? (
            <button type="button" onClick={nextStep} className="px-5 py-2.5 bg-[#299dc6] hover:bg-[#2389ae] text-white rounded-lg font-semibold text-sm shadow-sm transition-colors cursor-pointer">Next Step</button>
          ) : (
            <button type="submit" disabled={saving} className="px-6 py-2.5 bg-[#62975f] hover:bg-[#568451] text-white rounded-lg font-semibold text-sm shadow-sm transition-colors disabled:opacity-50 flex items-center gap-2 cursor-pointer">
              {saving ? <><svg className="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle><path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Saving...</> : 'Save Employee'}
            </button>
          )}
        </div>
      </form>
    </div>
  );
}
