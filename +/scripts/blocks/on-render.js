const onRender = (response, block, attributes) => {
    GLSR.Event.trigger(block, response, attributes);
}

export default onRender;
