const defaultConfig = require('@wordpress/scripts/config/webpack.config');

const getEntry = typeof defaultConfig.entry === 'function' ? defaultConfig.entry : () => defaultConfig.entry;

module.exports = {
	...defaultConfig,
	entry: async () => {
		const defaultEntry = await getEntry();
		return {
			...defaultEntry,
			'admin-options': './src/admin/options/index.js',
		};
	},
};
