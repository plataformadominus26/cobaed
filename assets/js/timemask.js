$(document).ready(function() {
    $('.timepicker-24h').inputmask({
        mask: 'Hh:Mm:Ss',
        placeholder: 'HH:MM:SS',
        hourFormat: 24, // Ensures 24-hour validation
        definitions: {
            'H': { // First digit of the hour (0-2)
                validator: '[0-2]',
                cardinality: 1,
                casing: 'upper'
            },
            'h': { // Second digit of the hour (0-9, or 0-3 if first digit is 2)
                validator: function(chrs, maskset, pos, strict, opts) {
                    var hourFirstDigit = maskset.buffer[pos - 1];
                    if (hourFirstDigit === '2') {
                        return chrs.match(/[0-3]/); // Allows 20-23
                    }
                    return chrs.match(/[0-9]/); // Allows 00-19
                },
                cardinality: 1,
                casing: 'upper'
            },
            'M': { // First digit of the minute (0-5)
                validator: '[0-5]',
                cardinality: 1,
                casing: 'upper'
            },
            'm': { // Second digit of the minute (0-9)
                validator: '[0-9]',
                cardinality: 1,
                casing: 'upper'
            },
            'S': { // First digit of the second (0-5)
                validator: '[0-5]',
                cardinality: 1,
                casing: 'upper'
            },
            's': { // Second digit of the second (0-9)
                validator: '[0-9]',
                cardinality: 1,
                casing: 'upper'
            }
        }
    });
});

 document.addEventListener('DOMContentLoaded', function() {
    
    document.querySelectorAll('.time-mask').forEach(attachTimeMask);
});