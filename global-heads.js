// טעינת הסקריפט החיצוני של גוגל
var gtScript = document.createElement('script');
gtScript.async = true;
gtScript.src = 'https://www.googletagmanager.com/gtag/js?id=G-9THXBWHW06';
document.head.appendChild(gtScript);

// הגדרת האנליטיקס
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'G-9THXBWHW06');
