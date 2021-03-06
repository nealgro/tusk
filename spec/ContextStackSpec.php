<?php

use Mockery as m;
use Tusk\ContextStack;

describe('ContextStack', function() {
    beforeEach(function() {
        $this->contextStack = new ContextStack();
    });

    afterEach(function() {
        m::close();
    });

    describe('execute()', function() {
        it('should push the context onto the stack, set it up, then pop it', function() {
            $outerContext = m::mock('Tusk\AbstractContext');
            $innerContext = m::mock('Tusk\AbstractContext');

            $innerContextCalled = false;

            $outerContext
                ->shouldReceive('setUp')
                ->with(false)
                ->once()
                ->andReturnUsing(function() use ($outerContext, $innerContext) {
                    expect($this->contextStack->getContext())->toBe($outerContext);
                    $this->contextStack->execute($innerContext);
                    expect($this->contextStack->getContext())->toBe($outerContext);
                })
            ;

            $innerContext
                ->shouldReceive('setUp')
                ->with(false)
                ->once()
                ->andReturnUsing(function() use ($innerContext, &$innerContextCalled) {
                    expect($this->contextStack->getContext())->toBe($innerContext);
                    $innerContextCalled = true;
                })
            ;

            expect($this->contextStack->getContext())->toBe(null);

            $this->contextStack->execute($outerContext);

            expect($this->contextStack->getContext())->toBe(null);
            expect($innerContextCalled)->toBe(true);
        });
    });
});
