import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom'
import Login from './components/auth/Login'
import Layout from './components/layout/Layout'
import Dashboard from './pages/Dashboard'
import EmployeeList from './pages/employees/EmployeeList'
import EmployeeCreate from './pages/employees/EmployeeCreate'
import EmployeeShow from './pages/employees/EmployeeShow'
import EmployeeEdit from './pages/employees/EmployeeEdit'
import './index.css'

function App() {
  return (
    <Router>
      <Routes>
        <Route path="/login" element={<Login />} />
        
        {/* Protected Routes inside Layout */}
        <Route element={<Layout />}>
          <Route path="/dashboard" element={<Dashboard />} />
          <Route path="/employees" element={<EmployeeList />} />
          <Route path="/employees/create" element={<EmployeeCreate />} />
          <Route path="/employees/:id" element={<EmployeeShow />} />
          <Route path="/employees/:id/edit" element={<EmployeeEdit />} />
          {/* We will add other routes here later */}
        </Route>

        {/* Default route redirects to login */}
        <Route path="*" element={<Navigate to="/login" replace />} />
      </Routes>
    </Router>
  )
}

export default App
