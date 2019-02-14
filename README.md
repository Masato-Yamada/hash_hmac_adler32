# hash_hmac with adler32 for php7.2

Above php7.2, hashing algorithm: adler32 is disabled in hash_hmac() method.  
I've implemented hash_hmac() with adler32 by pure php code referring original php source code for compatibility.

# usage
put this code snippet and call like this.
```
hash_hmac_adler32(string $data, string $key)
```
