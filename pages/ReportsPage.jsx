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