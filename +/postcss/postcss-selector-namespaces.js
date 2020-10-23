/**
 * Modified version of postcss-selector-namespace which allows multiple namespaces
 * @see https://github.com/topaxi/postcss-selector-namespace/tree/v3.0.1
 */
module.exports = require('postcss').plugin(
  'postcss-selector-namespaces',
  (options = {}) => {
    let {
      namespace = '.self',
      selfSelector = /:--namespace/,
      rootSelector = /:root/,
      ignoreRoot = true,
      dropRoot = true,
    } = options

    selfSelector = regexpToGlobalRegexp(selfSelector)

    return (css, result) => {
      const computedNamespace =
        typeof namespace === 'string'
          ? namespace
          : namespace(css.source.input.file)

      if (!computedNamespace) {
        return
      }

      css.walkRules(rule => {
        if (canNamespaceSelectors(rule)) {
          return
        }

        rule.selectors = rule.selectors.map(selector =>
          namespaceSelector(selector, computedNamespace),
        )
      })
    }

    function namespaceSelector(selector, computedNamespace) {
      if (hasRootSelector(selector)) {
        return dropRootSelector(selector)
      }
      const selectors = [];
      computedNamespace.split(',').forEach(namespace => {
        selectors.push(hasSelfSelector(selector) ? selector.replace(selfSelector, namespace) : `${namespace} ${selector}`)
      })
      return selectors.join(',')
    }

    function hasSelfSelector(selector) {
      selfSelector.lastIndex = 0

      return selfSelector.test(selector)
    }

    function hasRootSelector(selector) {
      return ignoreRoot && selector.search(rootSelector) === 0
    }

    function dropRootSelector(selector) {
      if (dropRoot) {
        return selector.replace(rootSelector, '').trim() || selector
      }

      return selector
    }
  },
)

/**
 * Returns true if the rule selectors can be namespaces
 *
 * @param {postcss.Rule} rule The rule to check
 * @return {boolean} whether the rule selectors can be namespaced or not
 */
function canNamespaceSelectors(rule) {
  return hasParentRule(rule) || parentIsAllowedAtRule(rule)
}

/**
 * Returns true if the parent rule is a not a media or supports atrule
 *
 * @param {postcss.Rule} rule The rule to check
 * @return {boolean} true if the direct parent is a keyframe rule
 */
function parentIsAllowedAtRule(rule) {
  return (
    rule.parent &&
    rule.parent.type === 'atrule' &&
    !/(?:media|supports|for)$/.test(rule.parent.name)
  )
}

/**
 * Returns true if any parent rule is of type 'rule'
 *
 * @param {postcss.Rule|postcss.Root|postcss.AtRule} rule The rule to check
 * @return {boolean} true if any parent rule is of type 'rule' else false
 */
function hasParentRule(rule) {
  if (!rule.parent) {
    return false
  }

  if (rule.parent.type === 'rule') {
    return true
  }

  return hasParentRule(rule.parent)
}

/**
 * Newer javascript engines allow setting flags when passing existing regexp
 * to the RegExp constructor, until then, we extract the regexp source and
 * build a new object.
 *
 * @param {RegExp|string} regexp The regexp to modify
 * @return {RegExp} The new regexp instance
 */
function regexpToGlobalRegexp(regexp) {
  let source = regexp instanceof RegExp ? regexp.source : regexp

  return new RegExp(source, 'g')
}
