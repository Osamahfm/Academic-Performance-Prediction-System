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
export function FeedbackPage() {
const [message, setMessage] = useState('');
const [rating, setRating] = useState(5);
const [status, setStatus] = useState(null);


const submit = async (e) => {
e.preventDefault();
setStatus('sending');
try {
const res = await fetch('/api/feedback', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ message, rating }) });
if (!res.ok) throw new Error('Failed');
setStatus('thanks'); setMessage(''); setRating(5);
} catch (err) {
setStatus('error');
}
};


return (
<div className="min-h-screen bg-gray-50 flex items-center justify-center p-6">
<div className="w-full max-w-2xl bg-white p-6 rounded shadow">
<h2 className="text-xl font-semibold mb-3">Feedback</h2>
<form onSubmit={submit} className="space-y-3">
<label className="block">
<span className="text-sm">How useful are the predictions?</span>
<select value={rating} onChange={(e) => setRating(Number(e.target.value))} className="mt-1 block p-2 rounded border">
<option value={5}>5 - Very useful</option>
<option value={4}>4 - Useful</option>
<option value={3}>3 - Neutral</option>
<option value={2}>2 - Not useful</option>
<option value={1}>1 - Misleading</option>
</select>
</label>
<label className="block">
<span className="text-sm">Any comments?</span>
<textarea value={message} onChange={(e) => setMessage(e.target.value)} rows={5} className="mt-1 block w-full rounded border p-2" />
</label>
<div className="flex justify-end">
<button type="submit" className="px-4 py-2 rounded bg-indigo-600 text-white">Send feedback</button>
</div>
</form>
{status === 'thanks' && <div className="mt-4 p-2 bg-green-50 text-green-800 rounded">Thanks — your feedback was received.</div>}
{status === 'error' && <div className="mt-4 p-2 bg-red-50 text-red-800 rounded">There was a problem sending your feedback.</div>}
</div>
</div>
);
}