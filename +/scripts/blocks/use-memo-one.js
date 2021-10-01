const { useRef, useState, useEffect } = wp.element;

function areInputsEqual(newInputs, lastInputs) {
    // no checks needed if the inputs length has changed
    if (newInputs.length !== lastInputs.length) {
        return false;
    }
    // Using for loop for speed. It generally performs better than array.every
    // https://github.com/alexreardon/memoize-one/pull/59
    for (let i = 0; i < newInputs.length; i++) {
        // using shallow equality check
        if (newInputs[i] !== lastInputs[i]) {
            return false;
        }
    }
    return true;
}
// getResult changes on every call,
// the inputs array changes on every call
export function useMemoOne(getResult, inputs) {
    // using useState to generate initial value as it is lazy
    const initial = useState(() => ({
        inputs,
        result: getResult(),
    }))[0];
    const isFirstRun = useRef(true);
    const committed = useRef(initial);
    // persist any uncommitted changes after they have been committed
    const useCache = isFirstRun.current || Boolean(inputs && committed.current.inputs && areInputsEqual(inputs, committed.current.inputs));
    // create a new cache if required
    const cache = useCache ? committed.current : {
        inputs,
        result: getResult(),
    };
    // commit the cache
    useEffect(() => {
        isFirstRun.current = false;
        committed.current = cache;
    }, [cache]);
    return cache.result;
}
