import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, Spinner } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import ServerSideRender from '@wordpress/server-side-render';
import metadata from './block.json';
import './style.scss';

registerBlockType(metadata.name, {
    edit: (props) => {
        const { attributes, setAttributes } = props;
        const { parentId } = attributes;

        const { dependencias, isResolving } = useSelect((select) => {
            const query = { per_page: 100, _fields: 'id,title,parent' };
            return {
                dependencias: select('core').getEntityRecords('postType', 'dependencia', query),
                isResolving: select('core/data').isResolving('core', 'getEntityRecords', ['postType', 'dependencia', query])
            };
        }, []);

        const blockProps = useBlockProps();

        let options = [{ value: 0, label: 'Toda la Universidad (Raíz)' }];
        if (dependencias) {
            options = [
                ...options,
                ...dependencias.map(dep => ({
                    value: dep.id,
                    label: dep.title.rendered
                }))
            ];
        }

        return (
            <div { ...blockProps }>
                <InspectorControls>
                    <PanelBody title="Configuración del Organigrama" initialOpen={true}>
                        {isResolving ? (
                            <Spinner />
                        ) : (
                            <SelectControl
                                label="Seleccionar Dependencia Origen (Padre)"
                                value={parentId}
                                options={options}
                                onChange={(value) => setAttributes({ parentId: parseInt(value) })}
                                help="El organigrama dibujará esta dependencia y todos sus descendientes."
                            />
                        )}
                    </PanelBody>
                </InspectorControls>

                {/* Preview en el Editor WYSIWYG (Lo que editas es lo que se ve) */}
                <div style={{ pointerEvents: 'none', border: '1px dashed transparent', padding: '10px' }} 
                     onMouseEnter={(e) => e.currentTarget.style.borderColor = 'rgba(0,121,78,0.3)'}
                     onMouseLeave={(e) => e.currentTarget.style.borderColor = 'transparent'}>
                    <ServerSideRender
                        block="vpinunf/organigrama"
                        attributes={ attributes }
                        LoadingResponsePlaceholder={ () => (
                            <div style={{ textAlign: 'center', padding: '20px' }}>
                                <Spinner />
                                <p style={{ color: '#666', marginTop: '10px' }}>Dibujando organigrama jerárquico...</p>
                            </div>
                        ) }
                    />
                </div>
            </div>
        );
    },
    save: () => null
});
