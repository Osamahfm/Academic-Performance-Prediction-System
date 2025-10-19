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
export function NotFound() {
return (
<div className="min-h-screen flex items-center justify-center bg-gray-50 p-6">
<div className="text-center">
<h1 className="text-4xl font-bold mb-2">404</h1>
<p className="text-lg mb-4">Page not found</p>
<a href="/" className="px-4 py-2 rounded bg-indigo-600 text-white">Go home</a>
</div>
</div>
);
}