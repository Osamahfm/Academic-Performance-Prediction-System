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
export function LoginPage() {
const push = useRouterPush();
const [email, setEmail] = useState('');
const [password, setPassword] = useState('');
const [error, setError] = useState(null);
const [loading, setLoading] = useState(false);


const handleSubmit = async (e) => {
e.preventDefault();
setError(null);
setLoading(true);
try {
const res = await fetch('/api/login', {
method: 'POST',
headers: { 'Content-Type': 'application/json' },
body: JSON.stringify({ email, password }),
});
const data = await res.json();
if (!res.ok) throw new Error(data.message || 'Login failed');
// expected: { token, role }
localStorage.setItem('token', data.token);
// redirect based on role
if (data.role === 'Admin') push('/dashboard/admin');
else if (data.role === 'Instructor') push('/dashboard/instructor');
else push('/dashboard/student');
} catch (err) {
setError(err.message);
} finally {
setLoading(false);
}
};


return (
<div className="min-h-screen flex items-center justify-center bg-gray-50 p-4">
<div className="w-full max-w-md bg-white rounded-2xl shadow p-6">
<h2 className="text-2xl font-semibold mb-4">Sign in</h2>
{error && <div className="bg-red-100 text-red-800 p-2 rounded mb-4">{error}</div>}
<form onSubmit={handleSubmit} className="space-y-4">
<label className="block">
<span className="text-sm">Email</span>
<input required type="email" value={email} onChange={(e) => setEmail(e.target.value)} className="mt-1 block w-full rounded-md border-gray-200 shadow-sm p-2" />
</label>
<label className="block">
<span className="text-sm">Password</span>
<input required type="password" value={password} onChange={(e) => setPassword(e.target.value)} className="mt-1 block w-full rounded-md border-gray-200 shadow-sm p-2" />
</label>
<button disabled={loading} className="w-full py-2 rounded-xl bg-indigo-600 text-white font-medium disabled:opacity-60">{loading ? 'Signing in...' : 'Sign in'}</button>
</form>
<div className="mt-4 text-center text-sm text-gray-600">
Don't have an account? <a href="/register" className="text-indigo-600 underline">Register</a>
</div>
</div>
</div>
);
}