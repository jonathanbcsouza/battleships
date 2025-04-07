// Get PHP sessions with fallback
const phpSessions = window.PHP_SESSIONS || [];
console.log('Raw PHP sessions:', phpSessions);

// Convert array of objects to a single configuration object
export const userDefinedConfigs = phpSessions.reduce((acc, session) => {
  if (session && session.config_name && session.config_value) {
    acc[session.config_name] = session.config_value;
  }
  return acc;
}, {});

// Make configs available globally for legacy code
window.USER_CONFIGS = userDefinedConfigs;

// Log configurations for debugging
console.log('Processed user configurations:', userDefinedConfigs);
