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
export function DiscussionForum() {
const [threads, setThreads] = useState([]);
const [loading, setLoading] = useState(true);
const [newThreadTitle, setNewThreadTitle] = useState('');
const [newThreadBody, setNewThreadBody] = useState('');


useEffect(() => {
fetch('/api/forum/threads').then((r) => r.json()).then((d) => { setThreads(d || sampleThreads()); setLoading(false); }).catch(() => { setThreads(sampleThreads()); setLoading(false); });
}, []);


const createThread = async () => {
if (!newThreadTitle || !newThreadBody) return alert('Please add title & body');
const res = await fetch('/api/forum/threads', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ title: newThreadTitle, body: newThreadBody }) });
if (res.ok) {
const data = await res.json();
setThreads([data, ...threads]);
setNewThreadTitle(''); setNewThreadBody('');
} else alert('Failed to create thread');
};


return (
<div className="min-h-screen bg-gray-50 p-6">
<div className="max-w-3xl mx-auto">
<h2 className="text-2xl font-semibold mb-3">Discussion forum</h2>
<div className="bg-white p-4 rounded shadow mb-4">
<input value={newThreadTitle} onChange={(e) => setNewThreadTitle(e.target.value)} placeholder="Thread title" className="w-full p-2 rounded border mb-2" />
<textarea value={newThreadBody} onChange={(e) => setNewThreadBody(e.target.value)} rows={4} placeholder="Describe your topic" className="w-full p-2 rounded border mb-2" />
<div className="flex justify-end">
<button onClick={createThread} className="px-4 py-2 rounded bg-indigo-600 text-white">Create thread</button>
</div>
</div>


{loading ? <div>Loading...</div> : (
<ul className="space-y-3">
{threads.map((t) => (
<li key={t.id} className="bg-white p-4 rounded shadow">
<h3 className="font-medium">{t.title}</h3>
<p className="text-sm text-gray-600">{t.body}</p>
<div className="text-xs text-gray-500 mt-2">{t.replies?.length || 0} replies</div>
</li>
))}
</ul>
)}
</div>
</div>
);
}