import app from 'flarum/forum/app';

app.initializers.add('hamcq/newpostmonitor', () => {
  console.log('[hamcq/newpostmonitor] Hello, forum!');
});
