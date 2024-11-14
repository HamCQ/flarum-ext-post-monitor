import app from 'flarum/common/app';

app.initializers.add('hamcq/newpostmonitor', () => {
  console.log('[hamcq/newpostmonitor] Hello, forum and admin!');
});
