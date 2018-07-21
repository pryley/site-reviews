- [ ] add polyfills for IE11+
- [ ] fix form submission with recaptcha and no-ajax class
- [ ] make tabs responsive (auto-collapsable)
- [ ] migrate site-reviews/local/review/create
- [ ] replace native javascript validation
- [ ] store counts (global, assigned_to, term_id): ['counts' => [0,0,0,0,0,0]]
- [ ] verify all v2 hooks


// onCreateReview
	// 1. build/increment counts
	// 2. build/increment assigned_to counts
	// 3. build/increment term_id counts

// onBeforeDeleteReview
	// 1. build/decrement counts
	// 2. build/decrement assigned_to counts
	// 3. build/decrement term_id counts

// onChangeReviewStatus
	// 1. build or increment/decrement counts
	// 2. build or increment/decrement term_id counts
	// 3. build or increment/decrement post_id counts

// onBeforeUpdateReview
	// onChangeReviewAssignedTo
		// 1. build/decrement old assigned_to counts
		// 2. build/increment new assigned_to counts
	// onChangeReviewCategory
		// 1. build/decrement old term_id counts
		// 2. build/increment new term_id counts
	// onChangeReviewRating
		// 1. build/decrement counts
		// 2. build/decrement term_id counts
		// 3. build/decrement post_id counts
		// 4. increment counts
		// 5. increment term_id counts
		// 6. increment post_id counts
	// onChangeReviewType
		// 1. build/decrement counts
		// 2. build/decrement term_id counts
		// 3. build/decrement post_id counts
		// 4. increment counts
		// 5. increment term_id counts
		// 6. increment post_id counts
