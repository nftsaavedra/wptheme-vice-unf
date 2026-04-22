import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import metadata from './block.json';

registerBlockType(metadata.name, {
    edit: (props) => {
        const { attributes } = props;
        const blockProps = useBlockProps();

        return (
            <div {...blockProps}>
                <ServerSideRender
                    block={metadata.name}
                    attributes={attributes}
                    EmptyResponsePlaceholder={() => (
                        <div style={{ padding: '60px', textAlign: 'center', border: '2px dashed #007cba', borderRadius: '8px', color: '#007cba' }}>
                            Agrega textos para la sección Socios desde Opciones VpinUnf en el menú de administración, y los logos desde el menú Socios.
                        </div>
                    )}
                />
            </div>
        );
    },
    save: () => null,
});
