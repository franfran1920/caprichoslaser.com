<?php

namespace ImageOptimizer\Classes\Image;

use ImageOptimizer\Classes\Basic_Enum;

final class Image_Status extends Basic_Enum {
	public const NOT_OPTIMIZED = 'not-optimized';
	public const OPTIMIZED = 'optimized';
	public const OPTIMIZATION_IN_PROGRESS = 'optimization-in-progress';
	public const OPTIMIZATION_FAILED = 'optimization-failed';
	public const RESTORING_IN_PROGRESS = 'restoring-in-progress';
	public const RESTORING_FAILED = 'restoring-failed';
	public const REOPTIMIZING_IN_PROGRESS = 'reoptimizing-in-progress';
	public const REOPTIMIZING_FAILED = 'reoptimizing-failed';
}
