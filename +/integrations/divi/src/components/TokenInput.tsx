import React, { useCallback, useEffect, useState } from 'react';
import { loggedFetch } from '@divi/rest';
import { type FieldLibrary } from '@divi/types';
// @ts-expect-error
import { TagInput } from '@divi/field-library';

interface TokenInputProps {
  name: string;
  value: string[]; // Array of IDs as strings
  shortcode: string;
  option: string;
  placeholder?: string;
  [key: string]: any;
}

const TokenInput: React.FC<TokenInputProps> = ({
  name,
  value = [],
  shortcode,
  option,
  placeholder = 'Search...',
  ...props
}) => {
  // @ts-expect-error
  const [options, setOptions] = useState<FieldLibrary.TagInput.Options>({});
  const [search, setSearch]   = useState<string>('');

  // Fetch options with AJAX.
  const fetchOptions = useCallback(async (querySearch: string = '') => {
    try {
      const params = new URLSearchParams({
        include: value.join(','),
        option,
        ...(querySearch && { search: querySearch }),
      });

      const response = await loggedFetch({
        method:    'GET',
        restRoute: `/site-reviews/v1/shortcode/${shortcode}?${params.toString()}`,
      });

      setOptions(
        Object.fromEntries(
          response.map((item: any) => [
            item.id.toString(),
            {
              label:         item.title,
              customContent: () => (
                <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                  <span>{item.title}</span>
                  <span style={{ color: '#999', fontSize: '12px' }}>{item.id}</span>
                </div>
              ),
            },
          ])
        )
      );
    } catch (error) {
      console.error('Failed to fetch options:', error);
      setOptions({});
    }
  }, [shortcode, option, value]);

  // Fetch on search or value change.
  useEffect(() => {
    fetchOptions(search);
  }, [search, fetchOptions]);

  // Handle search changes.
  const handleSearchChange = useCallback((inputValue: string) => {
    setSearch(inputValue);
  }, []);

  // Reset search on dropdown close.
  const handleDropdownClose = useCallback(() => {
    setSearch('');
  }, []);

  // Transform value: array of objects → array of strings.
  // @ts-expect-error
  const handleChange = useCallback(({ inputValue }: { inputValue: FieldLibrary.TagInput.TagOption[] }) => {
      // Extract just the IDs as strings.
      const stringValues = inputValue.map(tag => tag.value);

      // Call the parent onChange with transformed value.
      props.onChange?.({ inputValue: stringValues });
    },
    [props.onChange]
  );

  // Convert stored value (string array) to display format (object array).
  const displayValue = value.map(id => {
    const opt = options[id];
    return opt ? { value: id, label: opt.label } : { value: id, label: id };
  });

  return (
    <TagInput
      {...props}
      name={name}
      value={displayValue}
      options={options}
      onSearchChange={handleSearchChange}
      onDropdownClose={handleDropdownClose}
      onChange={handleChange}
      outputFormat="simple"
      searchPosition="dropdown"
      allowCustomTags={false}
      showDropdownIcon={false}
      placeholder={placeholder}
    />
  );
};

// Export with a unique field name for registration.
// @ts-expect-error
TokenInput.fieldName = 'site-reviews/token-input';

export default TokenInput;
