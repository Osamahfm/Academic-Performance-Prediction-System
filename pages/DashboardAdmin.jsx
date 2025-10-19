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
const Header = ({ title, children }) => (
<header className="flex items-center justify-between p-4 border-b bg-white">
<h1 className="text-xl font-semibold">{title}</h1>
<div className="flex items-center gap-2">{children}</div>
</header>
);


const Sidebar = ({ links = [] }) => (
<aside className="w-64 bg-white border-r p-4 hidden md:block">
<nav className="space-y-2">
{links.map((l) => (
<a key={l.to} href={l.to} className="block p-2 rounded hover:bg-gray-50">{l.label}</a>
))}
</nav>
</aside>
);
export function DashboardAdmin() {
const [usersCount, setUsersCount] = useState('--');
const [atRiskCount, setAtRiskCount] = useState('--');


useEffect(() => {
// fetch small dashboard summary (replace with real endpoint)
fetch('/api/admin/summary').then((r) => r.json()).then((d) => {
setUsersCount(d.users || 124);
setAtRiskCount(d.atRisk || 12);
}).catch(() => {
setUsersCount(124); setAtRiskCount(12);
});
}, []);


return (
<div className="min-h-screen bg-gray-50">
<Header title="Admin dashboard">
<button onClick={() => { localStorage.removeItem('token'); window.location.href = '/login'; }} className="px-3 py-1 rounded bg-red-50">Sign out</button>
</Header>
<div className="flex">
<Sidebar links={[{ to: '/dashboard/admin', label: 'Overview' }, { to: '/discussion', label: 'Forum' }, { to: '/reports', label: 'Reports' }]} />
<main className="flex-1 p-6">
<div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
<div className="bg-white p-4 rounded shadow">Users<br/><span className="text-2xl font-bold">{usersCount}</span></div>
<div className="bg-white p-4 rounded shadow">At-risk<br/><span className="text-2xl font-bold">{atRiskCount}</span></div>
<div className="bg-white p-4 rounded shadow">Settings<br/><a href="/settings" className="text-indigo-600 underline">Open</a></div>
</div>


<section className="mt-6">
<h2 className="text-lg font-medium mb-3">Trends</h2>
<div className="grid grid-cols-1 md:grid-cols-2 gap-4">
<div className="bg-white p-4 rounded shadow">
<ReportsMiniChart />
</div>
<div className="bg-white p-4 rounded shadow">
<RiskDistributionMini />
</div>
</div>
</section>
</main>
</div>
</div>
);
}