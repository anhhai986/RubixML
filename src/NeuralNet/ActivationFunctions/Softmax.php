<?php

namespace Rubix\ML\NeuralNet\ActivationFunctions;

use MathPHP\LinearAlgebra\Matrix;

/**
 * Softmax
 *
 * The Softmax function is a generalization of the Sigmoid function that squashes
 * each activation between 0 and 1, and all activations add up to 1.
 *
 * @category    Machine Learning
 * @package     Rubix/ML
 * @author      Andrew DalPino
 */
class Softmax implements ActivationFunction
{
    /**
     * Return a tuple of the min and max output value for this activation
     * function.
     *
     * @return array
     */
    public function range() : array
    {
        return [0, 1];
    }

    /**
     * Compute the output value.
     *
     * @param  \MathPHP\LinearAlgebra\Matrix  $z
     * @return \MathPHP\LinearAlgebra\Matrix
     */
    public function compute(Matrix $z) : Matrix
    {
        $activations = [[]];

        foreach ($z->asVectors() as $i => $vector) {
            $cache = [];

            foreach ($vector->getVector() as $j => $value) {
                $cache[$j] = exp($value);
            }

            $sigma = array_sum($cache) + self::EPSILON;

            foreach ($cache as $j => $value) {
                $activations[$j][$i] = $value / $sigma;
            }
        }

        return new Matrix($activations);
    }

    /**
     * Calculate the derivative of the activation function at a given output.
     *
     * @param  \MathPHP\LinearAlgebra\Matrix  $z
     * @param  \MathPHP\LinearAlgebra\Matrix  $computed
     * @return \MathPHP\LinearAlgebra\Matrix
     */
    public function differentiate(Matrix $z, Matrix $computed) : Matrix
    {
        return $computed->map(function ($activation) {
            return $activation * (1 - $activation);
        });
    }
}