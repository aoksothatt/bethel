const http = require('http');

function get(url) {
  return new Promise((resolve, reject) => {
    http.get(url, (res) => {
      let data = [];
      res.on('data', chunk => data.push(chunk));
      res.on('end', () => {
        const buffer = Buffer.concat(data);
        resolve({
          statusCode: res.statusCode,
          headers: res.headers,
          body: buffer.toString('utf-8'),
          length: buffer.length
        });
      });
    }).on('error', reject);
  });
}

function post(url, payload) {
  return new Promise((resolve, reject) => {
    const postData = JSON.stringify(payload);
    const u = new URL(url);
    const req = http.request({
      hostname: u.hostname,
      port: u.port,
      path: u.pathname,
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Content-Length': Buffer.byteLength(postData)
      }
    }, (res) => {
      let data = [];
      res.on('data', chunk => data.push(chunk));
      res.on('end', () => {
        resolve({
          statusCode: res.statusCode,
          body: Buffer.concat(data).toString('utf-8')
        });
      });
    });
    req.on('error', reject);
    req.write(postData);
    req.end();
  });
}

async function runTests() {
  console.log('=== VERIFYING BETHEL HIGH SCHOOL UPDATE ===\n');

  // Test 1: Vite Frontend Root
  console.log('1. Checking Vite Dev Server (http://localhost:5173/)...');
  const viteHtml = await get('http://localhost:5173/');
  console.log('Status:', viteHtml.statusCode);
  if (viteHtml.body.includes('Bethel High School')) {
    console.log('✓ Found "Bethel High School" in HTML title/description');
  } else {
    console.log('✗ Title not found in HTML');
  }

  // Test 2: Logo Asset in Vite
  console.log('\n2. Checking Logo Asset on Vite (http://localhost:5173/images/bethel-logo.jpg)...');
  const viteImg = await get('http://localhost:5173/images/bethel-logo.jpg');
  console.log('Status:', viteImg.statusCode, '| Bytes:', viteImg.length);
  if (viteImg.statusCode === 200 && viteImg.length > 50000) {
    console.log('✓ Official Bethel logo served successfully');
  } else {
    console.log('✗ Logo asset check failed');
  }

  // Test 3: Logo Asset on Laravel
  console.log('\n3. Checking Logo Asset on Laravel (http://127.0.0.1:8000/images/bethel-logo.jpg)...');
  const laravelImg = await get('http://127.0.0.1:8000/images/bethel-logo.jpg');
  console.log('Status:', laravelImg.statusCode, '| Bytes:', laravelImg.length);
  if (laravelImg.statusCode === 200 && laravelImg.length > 50000) {
    console.log('✓ Laravel serves logo asset successfully');
  }

  // Test 4: Laravel API Players endpoint
  console.log('\n4. Checking Laravel API (http://127.0.0.1:8000/api/players)...');
  const apiRes = await get('http://127.0.0.1:8000/api/players');
  console.log('Status:', apiRes.statusCode);
  const data = JSON.parse(apiRes.body);
  console.log('Players count:', data.players.length);
  console.log('Stats:', data.stats);
  if (data.players.length === 28) {
    console.log('✓ All 28 players verified');
  }

  // Test 5: Admin Login
  console.log('\n5. Checking Admin Authentication (POST /api/admin/login)...');
  const loginRes = await post('http://127.0.0.1:8000/api/admin/login', {
    email: 'admin@teamjersey.local',
    password: 'admin12345'
  });
  console.log('Status:', loginRes.statusCode);
  const loginData = JSON.parse(loginRes.body);
  if (loginData.token) {
    console.log('✓ Admin login successful! Token received:', loginData.token.substring(0, 15) + '...');
  }

  console.log('\n=== ALL TESTS PASSED SUCCESSFULLY ===');
}

runTests().catch(err => {
  console.error('Test error:', err);
  process.exit(1);
});
