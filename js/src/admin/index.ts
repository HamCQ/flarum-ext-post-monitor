import app from 'flarum/admin/app';

app.initializers.add('hamcq-newpostmonitor', () => {
  // console.log('[hamcq/newpostmonitor] Hello, admin!');
  app.extensionData
    .for('hamcq-newpostmonitor')
      .registerSetting({
        label: "监测开关",
        setting: 'hamcq.monitor_switch_new_post',
        type: 'switch',
      })
      .registerSetting({
        label: "摘要内容截取长度",
        setting: 'hamcq.monitor_switch_new_post_summary_length',
        type: 'int',
      })
      .registerSetting({
          label: "监测新发内容-机器人Webhook",
          setting: 'hamcq.monitor_new_post_robot_webhook',
          type: 'string',
      })
      .registerSetting({
        label: "监测开关",
        setting: 'hamcq.monitor_switch_new_user',
        type: 'switch',
      })
      .registerSetting({
        label: "监测新用户注册-机器人Webhook",
        setting: 'hamcq.monitor_new_user_robot_webhook',
        type: 'string',
      })
    
});
