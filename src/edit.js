import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, PanelRow, ToggleControl } from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';
import { dateI18n, __experimentalGetSettings as getDateSettings } from '@wordpress/date';

export default function Edit({ attributes, setAttributes }) {
    const { showId, showFirstName, showLastName, showEmail, showDate } = attributes;
    const [apiResponse, setApiResponse] = useState(null);
    const [headers, setHeaders] = useState(null);
    const [data, setData] = useState(null);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState(null);

    const settings = getDateSettings();
    const dateTimeFormat = `${settings.formats.date} ${settings.formats.time}`;

    useEffect(() => {
        setIsLoading(true);

        const formData = new FormData();
        formData.append('action', 'bdf_fetch_data');
        formData.append('security', bdfBlockData.nonce);

        fetch(bdfBlockData.ajaxurl, {
            method: 'POST',
            body: formData,
        })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    setApiResponse(result.data);
                } else {
                    setError(result.data.message || __( 'Failed to fetch data', 'bishal-data-fetcher' ));
                }
                setIsLoading(false);
            })
            .catch(err => {
                setError(err.message);
                setIsLoading(false);
            });
    }, []);

    useEffect(() => {
        if (apiResponse) {
            setHeaders(apiResponse.data.headers);
            setData(apiResponse.data.rows);
        }
    }, [apiResponse]);

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Column Visibility', 'bishal-data-fetcher')}>
                    <PanelRow>
                        <ToggleControl
                            label={__('Show ID', 'bishal-data-fetcher')}
                            checked={showId}
                            onChange={() => setAttributes({ showId: !showId })}
                        />
                    </PanelRow>
                    <PanelRow>
                        <ToggleControl
                            label={__('Show First Name', 'bishal-data-fetcher')}
                            checked={showFirstName}
                            onChange={() => setAttributes({ showFirstName: !showFirstName })}
                        />
                    </PanelRow>
                    <PanelRow>
                        <ToggleControl
                            label={__('Show Last Name', 'bishal-data-fetcher')}
                            checked={showLastName}
                            onChange={() => setAttributes({ showLastName: !showLastName })}
                        />
                    </PanelRow>
                    <PanelRow>
                        <ToggleControl
                            label={__('Show Email', 'bishal-data-fetcher')}
                            checked={showEmail}
                            onChange={() => setAttributes({ showEmail: !showEmail })}
                        />
                    </PanelRow>
                    <PanelRow>
                        <ToggleControl
                            label={__('Show Date', 'bishal-data-fetcher')}
                            checked={showDate}
                            onChange={() => setAttributes({ showDate: !showDate })}
                        />
                    </PanelRow>
                </PanelBody>
            </InspectorControls>

            <div {...useBlockProps()}>
                {isLoading && <p>{__('Loading...', 'bishal-data-fetcher')}</p>}
                {error && <p>{__('Error: ', 'bishal-data-fetcher')}{error}</p>}
                {data && (
                    <>
                        <h3>{apiResponse.data.title}</h3>
                        <table className="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    {showId && <th>{__('ID', 'bishal-data-fetcher')}</th>}
                                    {showFirstName && <th>{__('First Name', 'bishal-data-fetcher')}</th>}
                                    {showLastName && <th>{__('Last Name', 'bishal-data-fetcher')}</th>}
                                    {showEmail && <th>{__('Email', 'bishal-data-fetcher')}</th>}
                                    {showDate && <th>{__('Date', 'bishal-data-fetcher')}</th>}
                                </tr>
                            </thead>
                            <tbody>
                                {Object.values(data).map((item) => (
                                    <tr key={item.id}>
                                        {showId && <td>{item.id}</td>}
                                        {showFirstName && <td>{item.fname}</td>}
                                        {showLastName && <td>{item.lname}</td>}
                                        {showEmail && <td>{item.email}</td>}
                                        {showDate && (
                                            <td>{
                                                (() => {
                                                    const ts = Number(item.date);
                                                    const ms = Number(ts) * 1000;
                                                    return dateI18n(dateTimeFormat, ms);
                                                })()
                                            }</td>
                                        )}
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </>
                )}
            </div>
        </>
    );
}