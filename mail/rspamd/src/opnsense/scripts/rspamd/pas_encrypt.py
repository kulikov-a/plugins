#!/usr/local/bin/python3

"""
    Copyright (c) 2022 
    All rights reserved.

    Redistribution and use in source and binary forms, with or without
    modification, are permitted provided that the following conditions are met:

    1. Redistributions of source code must retain the above copyright notice,
     this list of conditions and the following disclaimer.

    2. Redistributions in binary form must reproduce the above copyright
     notice, this list of conditions and the following disclaimer in the
     documentation and/or other materials provided with the distribution.

    THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
    INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
    AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
    AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
    OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
    SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
    INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
    CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
    ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
    POSSIBILITY OF SUCH DAMAGE.

    --------------------------------------------------------------------------------------
    encrypt password with Rspamd rspamadm util
"""
import subprocess
import ujson
import argparse

if __name__ == '__main__':
    parser = argparse.ArgumentParser()
    parser.add_argument('--password', help='password to encrypt', default='')
    inputargs = parser.parse_args()

    result = dict()
    result['status'] = 'FAILED'
    password = inputargs.password
    try:
        sp = subprocess.run(['/usr/local/bin/rspamadm', 'pw', '--password', password], capture_output=True, text=True, check=True)
        if sp.returncode == 0:
            key = sp.stdout.split("\n")[0]
            result['password'] = key
            result['status'] = 'OK'
    except subprocess.CalledProcessError as e:
        result['reason'] = 'Return Code: %s. Stderr: %s.' % (e.returncode, e.stderr.decode().strip()[:255])

    print(ujson.dumps(result))
