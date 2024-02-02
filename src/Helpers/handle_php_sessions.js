const phpSessions = window.PHP_SESSIONS;

export const userDefinedConfigs = phpSessions.reduce((acc, session) => {
  acc[session.config_name] = session.config_value;
  return acc;
}, {});
