// See: https://github.com/KoryNunn/crel

const func = 'function';
const isNodeString = 'isNode';
const isType = (object, type) => typeof object === type;
const appendChild = (element, child) => {
    if (child !== null) {
        if (Array.isArray(child)) { // Support (deeply) nested child elements
            child.map(subChild => appendChild(element, subChild));
        } else {
            if (!dom[isNodeString](child)) {
                child = document.createTextNode(child);
            }
            element.appendChild(child);
        }
    }
};

function dom (element, settings) {
    // Define all used variables / shortcuts here, to make things smaller once compiled
    let args = arguments; // Note: assigned to a variable to assist compilers.
    let index = 1;
    let key;
    let attribute;
    // If first argument is an element, use it as is, otherwise treat it as a tagname
    element = dom.isElement(element) ? element : document.createElement(element);
    // Check if second argument is a settings object
    if (isType(settings, 'object') && !dom[isNodeString](settings) && !Array.isArray(settings)) {
        // Don't treat settings as a child
        index++;
        // Go through settings / attributes object, if it exists
        for (key in settings) {
            // Store the attribute into a variable, before we potentially modify the key
            attribute = settings[key];
            // Get mapped key / function, if one exists
            key = dom.attrMap[key] || key;
            // Note: We want to prioritise mapping over properties
            if (isType(key, func)) {
                key(element, attribute);
            } else if (isType(attribute, func)) { // ex. onClick property
                element[key] = attribute;
            } else {
                // Set the element attribute
                element.setAttribute(key, attribute);
            }
        }
    }
    // Loop through all arguments, if any, and append them to our element if they're not `null`
    for (; index < args.length; index++) {
        appendChild(element, args[index]);
    }
    return element;
}

// Used for mapping attribute keys to supported versions in bad browsers, or to custom functionality
dom.attrMap = {};
dom.isElement = object => object instanceof Element;
dom[isNodeString] = node => node instanceof Node;

export default dom;
