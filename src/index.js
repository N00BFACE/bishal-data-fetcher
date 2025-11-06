import { registerBlockType } from '@wordpress/blocks';

import Edit from './edit';
import blockJson from './block.json';

registerBlockType( blockJson.name, {
	...blockJson,
	edit: Edit,
});