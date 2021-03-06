<?php

namespace Rubix\ML\CrossValidation;

use Rubix\ML\Learner;
use Rubix\ML\Estimator;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\CrossValidation\Metrics\Metric;
use Rubix\ML\Specifications\EstimatorIsCompatibleWithMetric;
use InvalidArgumentException;

/**
 * Hold Out
 *
 * Hold Out is a quick and simple cross validation technique that uses a validation set
 * that is *held out* from the training data. The advantages of Hold Out is that the
 * validation score is quick to compute, however it does not allow the learner to *both*
 * train and test on all the data in the training set.
 *
 * @category    Machine Learning
 * @package     Rubix/ML
 * @author      Andrew DalPino
 */
class HoldOut implements Validator
{
    /**
     * The hold out ratio. i.e. the ratio of samples to use for testing.
     *
     * @var float
     */
    protected $ratio;

    /**
     * @param float $ratio
     * @throws \InvalidArgumentException
     */
    public function __construct(float $ratio = 0.2)
    {
        if ($ratio <= 0.0 or $ratio >= 1.0) {
            throw new InvalidArgumentException('Ratio must be between'
                . " 0 and 1, $ratio given.");
        }

        $this->ratio = $ratio;
    }

    /**
     * Test the estimator with the supplied dataset and return a validation score.
     *
     * @param \Rubix\ML\Learner $estimator
     * @param \Rubix\ML\Datasets\Labeled $dataset
     * @param \Rubix\ML\CrossValidation\Metrics\Metric $metric
     * @throws \InvalidArgumentException
     * @return float
     */
    public function test(Learner $estimator, Labeled $dataset, Metric $metric) : float
    {
        EstimatorIsCompatibleWithMetric::check($estimator, $metric);

        $dataset->randomize();

        [$testing, $training] = $dataset->labelType()->isCategorical()
            ? $dataset->stratifiedSplit($this->ratio)
            : $dataset->split($this->ratio);

        $estimator->train($training);

        $predictions = $estimator->predict($testing);

        return $metric->score($predictions, $testing->labels());
    }
}
