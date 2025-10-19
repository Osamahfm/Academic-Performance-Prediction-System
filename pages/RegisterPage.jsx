import React, { useState, useEffect } from 'react';
import { Line, Bar, Pie } from 'react-chartjs-2';
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, BarElement, ArcElement, Title, Tooltip, Legend } from 'chart.js';


ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, BarElement, ArcElement, Title, Tooltip, Legend);


// Utility: router helper (supports next/router or window.location)
const useRouterPush = () => {
try {
// eslint-disable-next-line global-require
const { useRouter } = require('next/router');
const r = useRouter();
return (path) => r.push(path);
} catch (e) {
return (path) => { window.location.href = path; };
}
};
export function RegisterPage() {
const push = useRouterPush();
const [form, setForm] = useState({ name: '', email: '', password: '', role: 'Student' });
const [loading, setLoading] = useState(false);
const [message, setMessage] = useState(null);


const handleChange = (k) => (e) => setForm({ ...form, [k]: e.target.value });


const handleSubmit = async (e) => {
e.preventDefault();
setLoading(true);
setMessage(null);
try {
const res = await fetch('/api/register', {
method: 'POST',
headers: { 'Content-Type': 'application/json' },
body: JSON.stringify(form),
});
const data = await res.json();
if (!res.ok) throw new Error(data.message || 'Registration failed');
setMessage('Registration successful — please check your email to verify your account.');
setTimeout(() => push('/login'), 2500);
} catch (err) {
setMessage(err.message);
} finally {
setLoading(false);
}
};


return (
<div className="min-h-screen flex items-center justify-center bg-gray-50 p-4">
<div className="w-full max-w-lg bg-white rounded-2xl shadow p-6">
<h2 className="text-2xl font-semibold mb-4">Create account</h2>
{message && <div className="p-3 mb-4 rounded bg-green-50 text-green-800">{message}</div>}
<form onSubmit={handleSubmit} className="grid gap-3">
<input required placeholder="Full name" value={form.name} onChange={handleChange('name')} className="p-2 rounded border" />
<input required type="email" placeholder="Email" value={form.email} onChange={handleChange('email')} className="p-2 rounded border" />
<input required type="password" placeholder="Password" value={form.password} onChange={handleChange('password')} className="p-2 rounded border" />
<select value={form.role} onChange={handleChange('role')} className="p-2 rounded border">
<option>Student</option>
<option>Instructor</option>
<option>Admin</option>
</select>
<button disabled={loading} className="py-2 rounded-xl bg-indigo-600 text-white">{loading ? 'Creating...' : 'Create account'}</button>
</form>
</div>
</div>
);
}   