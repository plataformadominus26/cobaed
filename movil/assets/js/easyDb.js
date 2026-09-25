// ============================================================================
// EasyDB - A Modern IndexedDB Wrapper with Automatic Version Management
// ============================================================================

/**
 * EasyDB - A modern, self-managing IndexedDB wrapper
 * Features:
 * - Automatic version management
 * - Dynamic index creation
 * - Metadata storage within DB
 * - Promise-based API
 * - Error handling & recovery
 */
class EasyDB {
    /**
     * @param {string} dbName - Database name
     * @param {string[]|string} stores - Array of store names or comma-separated string
     */
    constructor(dbName = "easyDB", stores = []) {
        this.dbName = dbName;
        this.db = null;
        this.stores = Array.isArray(stores) ? stores : stores.split(",").map(s => s.trim());
        this.version = 1; // Initial version, will be auto-managed
        this.isInitialized = false;
        
        // Internal state
        this._pendingIndexes = new Map();
        this._metadata = new Map();
    }
    /**
     * Initialize the database with automatic version management
     * @returns {Promise<EasyDB>} - Returns this instance for chaining
     */
    async init() {
        if (this.isInitialized) {
            return this;
        }

        try {
            console.log(`🔄 Initializing EasyDB "${this.dbName}"...`);
            
            // First, check if database exists and get its current version
            await this._determineVersion();
            console.log(`📋 Version determined: v${this.version}`);
            
            // Open database with determined version
            this.db = await this._openDatabase();
            console.log(`🔓 Database opened successfully`);
            
            // Initialize metadata system
            await this._initializeMetadata();
            console.log(`📊 Metadata system initialized`);
            
            // Load cached metadata
            await this._loadMetadataCache();
            console.log(`💾 Metadata cache loaded`);
            
            // Ensure database is ready before setting flag
            await this._validateDatabaseReady();
            
            // Set initialization flag
            this.isInitialized = true;
            console.log(`✅ EasyDB "${this.dbName}" fully initialized (v${this.version})`);
            
            return this;
        } catch (error) {
            console.error(`❌ Failed to initialize EasyDB "${this.dbName}":`, error);
            this.isInitialized = false;
            this.db = null;
            throw error;
        }
    }

    /**
     * Validate that database is ready for operations
     * @private
     */
    async _validateDatabaseReady() {
        if (!this.db) {
            throw new Error('Database connection is null');
        }

        // Test basic database functionality
        try {
            // Try to access object store names to ensure connection is valid
            const storeNames = Array.from(this.db.objectStoreNames);
            console.log(`🔍 Database validation: Found ${storeNames.length} stores`);
            
            // Verify our stores exist
            for (const storeName of this.stores) {
                if (!this.db.objectStoreNames.contains(storeName)) {
                    console.warn(`⚠️ Store '${storeName}' not found in database`);
                }
            }
            
            return true;
        } catch (error) {
            throw new Error(`Database validation failed: ${error.message}`);
        }
    }

    /**
     * Determine the appropriate database version
     * @private
     */
    async _determineVersion() {
    return new Promise((resolve) => {
        const request = indexedDB.open(this.dbName);
        
        request.onsuccess = (event) => {
            const db = event.target.result;
            const currentVersion = db.version;
            db.close();
            
            // Always increment version to force upgrade
            this.version = currentVersion + 1;
            console.log(`🔄 Database version incremented from ${currentVersion} to ${this.version}`);
            resolve();
        };
        
        request.onerror = () => {
            // Database doesn't exist, start with version 1
            this.version = 1;
            console.log('🆕 Creating new database with version 1');
            resolve();
        };
    });
}
    /**
     * Open database with proper upgrade handling
     * @private
     */
    async _openDatabase() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName, this.version);

            request.onupgradeneeded = (event) => {
                const db = event.target.result;
                const transaction = event.target.transaction;
                
                console.log(`🔄 Upgrading database from v${event.oldVersion} to v${event.newVersion}`);
                
                try {
                    this._createStores(db, transaction);
                    this._handlePendingIndexes(db, transaction);
                } catch (error) {
                    console.error('❌ Database upgrade failed:', error);
                    // Don't reject here - let the process continue
                }
            };

            request.onsuccess = (event) => {
                const db = event.target.result;
                
                // Handle version changes from other tabs
                db.onversionchange = () => {
                    console.warn('⚠️ Database version change detected, closing connection');
                    db.close();
                    this.db = null;
                    this.isInitialized = false;
                };
                
                resolve(db);
            };

            request.onerror = (event) => {
                reject(new Error(`Database open failed: ${event.target.error}`));
            };

            request.onblocked = () => {
                console.warn('⚠️ Database upgrade blocked by other connections');
            };
        });
    }

    /**
     * Create object stores during database upgrade
     * @private
     */
    _createStores(db, transaction) {
        // Create metadata store first (highest priority)
        if (!db.objectStoreNames.contains('_metadata')) {
            const metadataStore = db.createObjectStore('_metadata', {
                keyPath: 'key'
            });
            console.log('📋 Created _metadata store');
        }

        // Create application stores
        this.stores.forEach(storeName => {
            const trimmedName = storeName.trim();
            if (trimmedName && !db.objectStoreNames.contains(trimmedName)) {
                const store = db.createObjectStore(trimmedName, {
                    keyPath: '_ai_id',
                    autoIncrement: true
                });
                
                // Create default index for rKey
                store.createIndex('rKey_index', 'rKey', { unique: false });
                
                console.log(`📦 Created store: ${trimmedName}`);
            }
        });

        // Handle legacy stores (like 'lecturas' from your original code)
        this._handleLegacyStores(db, transaction);
    }

    /**
     * Handle legacy store requirements
     * @private
     */
    _handleLegacyStores(db, transaction) {
        // Handle the 'lecturas' store with special index from your original code
        if (db.objectStoreNames.contains('lecturas')) {
            const store = transaction.objectStore('lecturas');
            if (!store.indexNames.contains('periodo_sector_manzana')) {
                try {
                    store.createIndex('periodo_sector_manzana', 
                        ['periodo', 'sector', 'manzana'], 
                        { unique: false });
                    console.log('📋 Created periodo_sector_manzana index');
                } catch (error) {
                    console.warn('⚠️ Could not create periodo_sector_manzana index:', error);
                }
            }
        }
    }

    /**
     * Handle pending index creation during upgrade
     * @private
     */
    _handlePendingIndexes(db, transaction) {
        // This will be populated when indexes are scheduled for creation
        this._pendingIndexes.forEach((indexes, storeName) => {
            if (db.objectStoreNames.contains(storeName)) {
                const store = transaction.objectStore(storeName);
                indexes.forEach(indexName => {
                    try {
                        if (!store.indexNames.contains(indexName)) {
                            store.createIndex(indexName, indexName, { unique: false });
                            console.log(`📋 Created index '${indexName}' on store '${storeName}'`);
                        }
                    } catch (error) {
                        console.warn(`⚠️ Could not create index '${indexName}':`, error);
                    }
                });
            }
        });
        
        // Clear pending indexes after creation
        this._pendingIndexes.clear();
    }
    // ========================================================================
    // CORE DATA OPERATIONS
    // ========================================================================

    /**
     * Insert or update data in a store
     * @param {string} storeName - Name of the store
     * @param {Object|Array} data - Data to insert (single object or array)
     * @param {string} [key] - Optional key for rKey field
     * @param {string[]} [autoIndex] - Fields to automatically index
     * @returns {Promise<number|number[]>} - ID(s) of inserted record(s)
     */
    async push(storeName, data, key = null, autoIndex = null) {
        this._validateInitialization();
        this._validateStoreName(storeName);

        try {
            // Handle array of data
            if (Array.isArray(data)) {
                return await this._pushMultiple(storeName, data, key, autoIndex);
            }

            // Handle single data object
            return await this._pushSingle(storeName, data, key, autoIndex);
        } catch (error) {
            console.error(`❌ Push failed for store '${storeName}':`, error);
            throw error;
        }
    }

    /**
     * Push single record
     * @private
     */
    async _pushSingle(storeName, data, key, autoIndex) {
        // Ensure data has rKey
        if (!data.rKey) {
            data.rKey = key || this._generateKey();
        }

        // Auto-create indexes if specified
        if (autoIndex && autoIndex.length > 0) {
            await this._scheduleIndexCreation(storeName, data, autoIndex);
        }

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(storeName, 'readwrite');
            const store = transaction.objectStore(storeName);
            const request = store.put(data);

            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(new Error(`Insert failed: ${request.error}`));
            
            transaction.onerror = () => reject(new Error(`Transaction failed: ${transaction.error}`));
        });
    }

    /**
     * Push multiple records efficiently
     * @private
     */
    async _pushMultiple(storeName, dataArray, key, autoIndex) {
        const results = [];
        
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(storeName, 'readwrite');
            const store = transaction.objectStore(storeName);
            let completed = 0;

            // Process each item
            dataArray.forEach((data, index) => {
                if (!data.rKey) {
                    data.rKey = key || this._generateKey(index);
                }

                const request = store.put(data);
                request.onsuccess = () => {
                    results[index] = request.result;
                    if (++completed === dataArray.length) {
                        resolve(results);
                    }
                };
                request.onerror = () => reject(new Error(`Batch insert failed at index ${index}`));
            });

            transaction.onerror = () => reject(new Error(`Batch transaction failed`));
        });
    }
    /**
     * Fetch all records from a store
     * @param {string} storeName - Name of the store
     * @returns {Promise<Array>} - Array of all records
     */
    async fetchAll(storeName) {
        this._validateInitialization();
        this._validateStoreName(storeName);

        if (!this.db.objectStoreNames.contains(storeName)) {
            console.warn(`⚠️ Store '${storeName}' does not exist`);
            return [];
        }

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(storeName, 'readonly');
            const store = transaction.objectStore(storeName);
            const request = store.getAll();

            request.onsuccess = () => {
                resolve(request.result || []);}
            request.onerror = () => {
                reject(new Error(`Fetch all failed: ${request.error}`));
            }
            transaction.onerror = () => {
                reject(new Error(`Transaction failed: ${transaction.error}`));
            }
        });
    }

    /**
     * Fetch a single record by ID
     * @param {string} storeName - Name of the store
     * @param {number} id - Record ID
     * @returns {Promise<Object|null>} - Record or null if not found
     */
    async fetch(storeName, id) {
        this._validateInitialization();
        this._validateStoreName(storeName);

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(storeName, 'readonly');
            const store = transaction.objectStore(storeName);
            const request = store.get(id);

            request.onsuccess = () => resolve(request.result || null);
            request.onerror = () => reject(new Error(`Fetch failed: ${request.error}`));
            transaction.onerror = () => reject(new Error(`Transaction failed: ${transaction.error}`));
        });
    }

    /**
     * Fetch record by rKey
     * @param {string} storeName - Name of the store
     * @param {string} rKey - Record key
     * @returns {Promise<Object|null>} - Record or null if not found
     */
    async fetchByKey(storeName, rKey) {
        this._validateInitialization();
        this._validateStoreName(storeName);

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(storeName, 'readonly');
            const store = transaction.objectStore(storeName);
            
            try {
                const index = store.index('rKey_index');
                const request = index.get(rKey);

                request.onsuccess = () => resolve(request.result || null);
                request.onerror = () => reject(new Error(`Fetch by key failed: ${request.error}`));
            } catch (error) {
                // Fallback to manual search if index doesn't exist
                this._fetchByKeyManual(storeName, rKey).then(resolve).catch(reject);
            }
            
            transaction.onerror = () => reject(new Error(`Transaction failed: ${transaction.error}`));
        });
    }
    /**
     * Query records with advanced filtering
     * @param {string} storeName - Name of the store
     * @param {Object|Function} filter - Filter object or function
     * @param {Object} [options] - Query options (limit, offset, orderBy)
     * @returns {Promise<Array>} - Filtered records
     */
    async query(storeName, filter = {}, options = {}) {
        this._validateInitialization();
        this._validateStoreName(storeName);

        const results = await this.fetchAll(storeName);
        let filtered = results;

        // Apply filter
        if (typeof filter === 'function') {
            filtered = results.filter(filter);
        } else if (filter && typeof filter === 'object') {
            filtered = results.filter(record => {
                return Object.keys(filter).every(key => {
                    const filterValue = filter[key];
                    const recordValue = record[key];
                    
                    if (typeof filterValue === 'object' && filterValue !== null) {
                        // Handle operators like { $gt: 10, $lt: 20 }
                        return this._evaluateOperators(recordValue, filterValue);
                    }
                    
                    return recordValue == filterValue; // Loose equality for flexibility
                });
            });
        }

        // Apply ordering
        if (options.orderBy) {
            const { field, direction = 'asc' } = options.orderBy;
            filtered.sort((a, b) => {
                const aVal = a[field];
                const bVal = b[field];
                const comparison = aVal < bVal ? -1 : aVal > bVal ? 1 : 0;
                return direction === 'desc' ? -comparison : comparison;
            });
        }

        // Apply pagination
        if (options.offset || options.limit) {
            const start = options.offset || 0;
            const end = options.limit ? start + options.limit : filtered.length;
            filtered = filtered.slice(start, end);
        }

        return filtered;
    }

    /**
     * Legacy query method for backward compatibility
     * @param {string} storeName - Name of the store
     * @param {string} field - Field name to query
     * @param {*} value - Value to match
     * @returns {Promise<Array>} - Matching records
     */
    async queryLegacy(storeName, field, value) {
        return this.query(storeName, { [field]: value });
    }

    /**
     * Get an iterator for all records (memory efficient)
     * @param {string} storeName - Name of the store
     * @returns {AsyncIterator} - Async iterator for records
     */
    async *queryAll(storeName) {
        this._validateInitialization();
        this._validateStoreName(storeName);

        if (!this.db.objectStoreNames.contains(storeName)) {
            return;
        }

        const transaction = this.db.transaction(storeName, 'readonly');
        const store = transaction.objectStore(storeName);

        return new Promise((resolve, reject) => {
            const request = store.openCursor();
            const results = [];

            request.onsuccess = (event) => {
                const cursor = event.target.result;
                if (cursor) {
                    results.push(cursor.value);
                    cursor.continue();
                } else {
                    resolve(results);
                }
            };

            request.onerror = () => reject(new Error(`Query all failed: ${request.error}`));
        }).then(function* (results) {
            for (const result of results) {
                yield result;
            }
        });
    }
    /**
     * Update a record by ID
     * @param {string} storeName - Name of the store
     * @param {number} id - Record ID
     * @param {Object} newData - Data to update
     * @returns {Promise<Object>} - Updated record
     */
    async update(storeName, id, newData) {
        this._validateInitialization();
        this._validateStoreName(storeName);

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(storeName, 'readwrite');
            const store = transaction.objectStore(storeName);
            const getRequest = store.get(id);

            getRequest.onsuccess = (event) => {
                const existingRecord = event.target.result;
                if (!existingRecord) {
                    reject(new Error(`Record with ID ${id} not found`));
                    return;
                }

                const updatedRecord = { ...existingRecord, ...newData };
                const putRequest = store.put(updatedRecord);

                putRequest.onsuccess = () => resolve(updatedRecord);
                putRequest.onerror = () => reject(new Error(`Update failed: ${putRequest.error}`));
            };

            getRequest.onerror = () => reject(new Error(`Fetch for update failed: ${getRequest.error}`));
            transaction.onerror = () => reject(new Error(`Transaction failed: ${transaction.error}`));
        });
    }

    /**
     * Update a record by rKey
     * @param {string} storeName - Name of the store
     * @param {string} rKey - Record key
     * @param {Object} newData - Data to update
     * @returns {Promise<Object>} - Updated record
     */
    async updateByKey(storeName, rKey, newData) {
        const existingRecord = await this.fetchByKey(storeName, rKey);
        if (!existingRecord) {
            throw new Error(`Record with rKey '${rKey}' not found`);
        }
        return this.update(storeName, existingRecord._ai_id, newData);
    }

    /**
     * Delete a record by ID
     * @param {string} storeName - Name of the store
     * @param {number} id - Record ID
     * @returns {Promise<boolean>} - Success status
     */
    async delete(storeName, id) {
        this._validateInitialization();
        this._validateStoreName(storeName);

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(storeName, 'readwrite');
            const store = transaction.objectStore(storeName);
            const request = store.delete(id);

            request.onsuccess = () => resolve(true);
            request.onerror = () => reject(new Error(`Delete failed: ${request.error}`));
            transaction.onerror = () => reject(new Error(`Transaction failed: ${transaction.error}`));
        });
    }

    /**
     * Delete records matching a filter
     * @param {string} storeName - Name of the store
     * @param {Object|Function} filter - Filter criteria
     * @returns {Promise<number>} - Number of deleted records
     */
    async deleteWhere(storeName, filter) {
        const recordsToDelete = await this.query(storeName, filter);
        let deletedCount = 0;

        for (const record of recordsToDelete) {
            await this.delete(storeName, record._ai_id);
            deletedCount++;
        }

        return deletedCount;
    }
  // ========================================================================
    // STORE MANAGEMENT OPERATIONS
    // ========================================================================

    /**
     * Clear all data from one or more stores
     * @param {string|string[]} storeNames - Store name(s) to clear
     * @returns {Promise<string[]>} - Success messages
     */
    async zap(storeNames) {
        this._validateInitialization();
        
        const stores = Array.isArray(storeNames) ? storeNames : [storeNames];
        const results = [];

        for (const storeName of stores) {
            this._validateStoreName(storeName);
            
            try {
                await new Promise((resolve, reject) => {
                    const transaction = this.db.transaction(storeName, 'readwrite');
                    const store = transaction.objectStore(storeName);
                    const request = store.clear();

                    request.onsuccess = () => {
                        const message = `✅ Cleared all records from '${storeName}'`;
                        results.push(message);
                        resolve();
                    };
                    
                    request.onerror = () => reject(new Error(`Clear failed: ${request.error}`));
                    transaction.onerror = () => reject(new Error(`Transaction failed: ${transaction.error}`));
                });
            } catch (error) {
                console.error(`❌ Failed to clear store '${storeName}':`, error);
                results.push(`❌ Failed to clear '${storeName}': ${error.message}`);
            }
        }

        return results;
    }

    /**
     * Count records in a store with optional filtering
     * @param {string} storeName - Name of the store
     * @param {Object|Function} [filter] - Optional filter
     * @returns {Promise<number>} - Number of records
     */
    async count(storeName, filter = null) {
        this._validateInitialization();
        this._validateStoreName(storeName);

        if (!filter) {
            // Fast count without filtering
            return new Promise((resolve, reject) => {
                const transaction = this.db.transaction(storeName, 'readonly');
                const store = transaction.objectStore(storeName);
                const request = store.count();

                request.onsuccess = () => resolve(request.result);
                request.onerror = () => reject(new Error(`Count failed: ${request.error}`));
                transaction.onerror = () => reject(new Error(`Transaction failed: ${transaction.error}`));
            });
        } else {
            // Count with filtering
            const filteredRecords = await this.query(storeName, filter);
            return filteredRecords.length;
        }
    }

    /**
     * Get the size of a store in bytes
     * @param {string} storeName - Name of the store
     * @returns {Promise<string>} - Formatted size string
     */
    async size(storeName) {
        this._validateInitialization();
        this._validateStoreName(storeName);

        const records = await this.fetchAll(storeName);
        const totalBytes = records.reduce((acc, record) => {
            return acc + JSON.stringify(record).length;
        }, 0);

        return this._formatBytes(totalBytes);
    }

    /**
     * Get information about the database
     * @returns {Promise<Object>} - Database information
     */
    async info() {
        this._validateInitialization();

        const info = {
            name: this.dbName,
            version: this.version,
            stores: [],
            totalSize: 0
        };

        for (const storeName of this.db.objectStoreNames) {
            if (storeName === '_metadata') continue; // Skip internal store
            
            try {
                const count = await this.count(storeName);
                const sizeStr = await this.size(storeName);
                const sizeBytes = await this._getSizeInBytes(storeName);
                
                info.stores.push({
                    name: storeName,
                    count,
                    size: sizeStr,
                    sizeBytes
                });
                
                info.totalSize += sizeBytes;
            } catch (error) {
                console.warn(`Could not get info for store '${storeName}':`, error);
            }
        }

        info.totalSizeFormatted = this._formatBytes(info.totalSize);
        
        return info;
    }
    // ========================================================================
    // METADATA AND VERSION MANAGEMENT
    // ========================================================================

    /**
     * Initialize metadata system
     * @private
     */
    async _initializeMetadata() {
        try {
            // Set initial version if not exists
            const storedVersion = await this.getMetadata('version');
            if (!storedVersion) {
                await this.setMetadata('version', this.version);
                console.log(`📋 Initialized version tracking: v${this.version}`);
            } else if (storedVersion !== this.version) {
                this.version = storedVersion;
            }

            // Migrate from localStorage if needed
            await this._migrateFromLocalStorage();
        } catch (error) {
            console.warn('⚠️ Could not initialize metadata:', error);
        }
    }

    /**
     * Load metadata into memory cache
     * @private
     */
    async _loadMetadataCache() {
        try {
            const pendingIndexes = await this.getMetadata('pendingIndexes');
            if (pendingIndexes) {
                this._pendingIndexes.clear();
                Object.keys(pendingIndexes).forEach(storeName => {
                    this._pendingIndexes.set(storeName, pendingIndexes[storeName]);
                });
            }
        } catch (error) {
            console.warn('⚠️ Could not load metadata cache:', error);
        }
    }

    /**
     * Get metadata value
     * @param {string} key - Metadata key
     * @returns {Promise<*>} - Metadata value or null
     */
    async getMetadata(key) {
        if (!this.db || !this.db.objectStoreNames.contains('_metadata')) {
            return null;
        }

        try {
            return new Promise((resolve, reject) => {
                const transaction = this.db.transaction('_metadata', 'readonly');
                const store = transaction.objectStore('_metadata');
                const request = store.get(key);

                request.onsuccess = () => {
                    const result = request.result;
                    resolve(result ? result.value : null);
                };
                
                request.onerror = () => reject(new Error(`Metadata get failed: ${request.error}`));
                transaction.onerror = () => reject(new Error(`Transaction failed: ${transaction.error}`));
            });
        } catch (error) {
            console.warn(`⚠️ Could not get metadata '${key}':`, error);
            return null;
        }
    }

    /**
     * Set metadata value
     * @param {string} key - Metadata key
     * @param {*} value - Metadata value
     * @returns {Promise<void>}
     */
    async setMetadata(key, value) {
        if (!this.db || !this.db.objectStoreNames.contains('_metadata')) {
            console.warn('⚠️ Metadata store not available');
            return;
        }

        try {
            return new Promise((resolve, reject) => {
                const transaction = this.db.transaction('_metadata', 'readwrite');
                const store = transaction.objectStore('_metadata');
                const request = store.put({ key, value });

                request.onsuccess = () => resolve();
                request.onerror = () => reject(new Error(`Metadata set failed: ${request.error}`));
                transaction.onerror = () => reject(new Error(`Transaction failed: ${transaction.error}`));
            });
        } catch (error) {
            console.warn(`⚠️ Could not set metadata '${key}':`, error);
        }
    }

    /**
     * Migrate data from localStorage
     * @private
     */
    async _migrateFromLocalStorage() {
        const localStorageKey = `pendingIndexes_${this.dbName}`;
        const localStorageData = localStorage.getItem(localStorageKey);
        
        if (localStorageData) {
            try {
                const pendingIndexes = JSON.parse(localStorageData);
                await this.setMetadata('pendingIndexes', pendingIndexes);
                localStorage.removeItem(localStorageKey);
                console.log('✅ Migrated pending indexes from localStorage');
            } catch (error) {
                console.warn('⚠️ Could not migrate from localStorage:', error);
            }
        }
    }
    // ========================================================================
    // INDEX MANAGEMENT
    // ========================================================================

    /**
     * Schedule index creation for next database upgrade
     * @private
     */
    async _scheduleIndexCreation(storeName, data, fieldsToIndex) {
        const newIndexes = [];
        
        // Check which indexes need to be created
        fieldsToIndex.forEach(fieldName => {
            if (data.hasOwnProperty(fieldName)) {
                // Check if index already exists
                try {
                    const transaction = this.db.transaction(storeName, 'readonly');
                    const store = transaction.objectStore(storeName);
                    if (!store.indexNames.contains(fieldName)) {
                        newIndexes.push(fieldName);
                    }
                } catch (error) {
                    newIndexes.push(fieldName); // Assume it needs to be created
                }
            }
        });

        if (newIndexes.length === 0) {
            return;
        }

        // Add to pending indexes
        if (!this._pendingIndexes.has(storeName)) {
            this._pendingIndexes.set(storeName, []);
        }

        const existingPending = this._pendingIndexes.get(storeName);
        newIndexes.forEach(indexName => {
            if (!existingPending.includes(indexName)) {
                existingPending.push(indexName);
                console.log(`📋 Scheduled index creation: '${indexName}' on '${storeName}'`);
            }
        });

        // Save to metadata
        const allPendingIndexes = {};
        this._pendingIndexes.forEach((indexes, store) => {
            allPendingIndexes[store] = indexes;
        });
        await this.setMetadata('pendingIndexes', allPendingIndexes);

        // Trigger database upgrade
        if (newIndexes.length > 0) {
            setTimeout(() => this._upgradeForIndexes(), 100);
        }
    }

    /**
     * Upgrade database version to create pending indexes
     * @private
     */
    async _upgradeForIndexes() {
        if (this._pendingIndexes.size === 0) {
            return;
        }

        try {
            console.log('🔄 Upgrading database for index creation...');
            
            // Close current connection
            this.db.close();
            this.isInitialized = false;
            
            // Increment version
            this.version += 1;
            await this.setMetadata('version', this.version);
            
            // Reinitialize with new version
            await this.init();
            
            console.log('✅ Database upgrade completed');
        } catch (error) {
            console.error('❌ Database upgrade failed:', error);
        }
    }

    /**
     * Convenience method to push data with automatic indexing
     * @param {string} storeName - Name of the store
     * @param {Object|Array} data - Data to insert
     * @param {string} [key] - Optional key for rKey field
     * @param {...string} fieldsToIndex - Fields to automatically index
     * @returns {Promise<number|number[]>} - ID(s) of inserted record(s)
     */
    async pushWithIndex(storeName, data, key, ...fieldsToIndex) {
        return this.push(storeName, data, key, fieldsToIndex);
    }

    // ========================================================================
    // DATABASE MANAGEMENT
    // ========================================================================

    /**
     * Close the database connection
     * @returns {Promise<string>} - Success message
     */
    async close() {
        if (this.db) {
            this.db.close();
            this.db = null;
            this.isInitialized = false;
            console.log(`🔒 Database '${this.dbName}' connection closed`);
        }
        return 'Database connection closed';
    }

    /**
     * Delete the entire database
     * @param {boolean} [reloadPage=false] - Whether to reload the page after deletion
     * @returns {Promise<string>} - Success message
     */
    async deleteDatabase(reloadPage = false) {
        return new Promise((resolve, reject) => {
            // Close active connection
            if (this.db) {
                this.db.close();
                this.db = null;
                this.isInitialized = false;
                console.log('🔒 Database connection closed before deletion');
            }

            const deleteRequest = indexedDB.deleteDatabase(this.dbName);

            deleteRequest.onsuccess = () => {
                const message = `✅ Database '${this.dbName}' deleted successfully`;
                console.log(message);
                
                if (reloadPage && typeof location !== 'undefined') {
                    location.reload();
                }
                
                resolve(message);
            };

            deleteRequest.onerror = (event) => {
                const error = new Error(`Delete database failed: ${event.target.error}`);
                reject(error);
            };

            deleteRequest.onblocked = () => {
                console.warn('⚠️ Database deletion blocked by other connections');
                // Don't reject, just warn - deletion may still succeed
            };
        });
    }
    // ========================================================================
    // HELPER METHODS
    // ========================================================================

    /**
     * Validate that the database is initialized
     * @private
     */
    _validateInitialization() {
        if (!this.isInitialized || !this.db) {
            throw new Error('Database not initialized. Call init() first.');
        }
    }

    /**
     * Validate store name
     * @private
     */
    _validateStoreName(storeName) {
        if (!storeName || typeof storeName !== 'string') {
            throw new Error('Store name must be a non-empty string');
        }
    }

    /**
     * Generate a unique key
     * @private
     */
    _generateKey(suffix = '') {
        const timestamp = Date.now();
        const random = Math.random().toString(36).substring(2, 8);
        return `${timestamp}_${random}${suffix ? '_' + suffix : ''}`;
    }

    /**
     * Fetch by key manually (fallback when index doesn't exist)
     * @private
     */
    async _fetchByKeyManual(storeName, rKey) {
        const allRecords = await this.fetchAll(storeName);
        return allRecords.find(record => record.rKey === rKey) || null;
    }

    /**
     * Evaluate query operators
     * @private
     */
    _evaluateOperators(value, operators) {
        return Object.keys(operators).every(op => {
            const opValue = operators[op];
            switch (op) {
                case '$gt': return value > opValue;
                case '$gte': return value >= opValue;
                case '$lt': return value < opValue;
                case '$lte': return value <= opValue;
                case '$ne': return value != opValue;
                case '$eq': return value == opValue;
                case '$in': return Array.isArray(opValue) && opValue.includes(value);
                case '$nin': return Array.isArray(opValue) && !opValue.includes(value);
                case '$regex': return new RegExp(opValue).test(String(value));
                default: return true;
            }
        });
    }

    /**
     * Format bytes to human readable string
     * @private
     */
    _formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    /**
     * Get size in bytes for a store
     * @private
     */
    async _getSizeInBytes(storeName) {
        const records = await this.fetchAll(storeName);
        return records.reduce((acc, record) => {
            return acc + JSON.stringify(record).length;
        }, 0);
    }

    // ========================================================================
    // LEGACY COMPATIBILITY METHODS
    // ========================================================================

    /**
     * Legacy method aliases for backward compatibility
     */
    
    // Alias for fetchByKey
    async fetchrKey(storeName, rKey) {
        return this.fetchByKey(storeName, rKey);
    }

    // Alias for updateByKey  
    async updaterKey(storeName, rKey, newData, field = null) {
        if (field && typeof newData === 'string') {
            // Legacy format: updaterKey(store, rKey, fieldName, value)
            return this.updateByKey(storeName, rKey, { [newData]: field });
        }
        return this.updateByKey(storeName, rKey, newData);
    }

    // Legacy query method
   async query(storeName, filter, value) {
    this._validateInitialization();
    this._validateStoreName(storeName);

    // Handle different calling patterns
    if (arguments.length === 3 && typeof filter === 'string') {
        // Legacy format: query(store, field, value)
        const results = await this.fetchAll(storeName);
        return results.filter(record => record[filter] == value);
    } else {
        // New format: query(store, filter, options)
        const actualFilter = filter;
        const options = value || {};

        const results = await this.fetchAll(storeName);
        let filtered = results;

        // Apply filter
        if (typeof actualFilter === 'function') {
            filtered = results.filter(actualFilter);
        } else if (actualFilter && typeof actualFilter === 'object') {
            filtered = results.filter(record => {
                return Object.keys(actualFilter).every(key => {
                    const filterValue = actualFilter[key];
                    const recordValue = record[key];
                    
                    if (typeof filterValue === 'object' && filterValue !== null) {
                        return this._evaluateOperators(recordValue, filterValue);
                    }
                    
                    if (typeof recordValue === 'string' && typeof filterValue === 'string') {
                        return recordValue.trim().toLowerCase() == filterValue.trim().toLowerCase();
                    }
                    return recordValue == filterValue;
                });
            });
        }

        // Apply ordering and pagination...
        if (options.orderBy) {
            const { field, direction = 'asc' } = options.orderBy;
            filtered.sort((a, b) => {
                const aVal = a[field];
                const bVal = b[field];
                const comparison = aVal < bVal ? -1 : aVal > bVal ? 1 : 0;
                return direction === 'desc' ? -comparison : comparison;
            });
        }

        if (options.offset || options.limit) {
            const start = options.offset || 0;
            const end = options.limit ? start + options.limit : filtered.length;
            filtered = filtered.slice(start, end);
        }

        return filtered;
    }
}

    // ========================================================================
    // SPECIAL METHODS (from your original code)
    // ========================================================================

    /**
     * Special method for fetching 'manzana' data (from your original code)
     * @param {string} periodo - Period value
     * @param {string} sector - Sector value  
     * @param {string} manzana - Manzana value
     * @returns {Promise<Array>} - Matching records
     */
    async fetchManzana(periodo, sector, manzana) {
        this._validateInitialization();
        
        if (!this.db.objectStoreNames.contains('lecturas')) {
            return [];
        }

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['lecturas'], 'readonly');
            const store = transaction.objectStore('lecturas');
            
            try {
                const index = store.index('periodo_sector_manzana');
                const key = [String(periodo), String(sector), String(manzana)];
                const request = index.getAll(key);
                
                request.onsuccess = (event) => resolve(event.target.result || []);
                request.onerror = (event) => reject(new Error(`fetchManzana failed: ${event.target.error}`));
            } catch (error) {
                // Fallback to manual search if index doesn't exist
                this.query('lecturas', record => 
                    String(record.periodo) === String(periodo) &&
                    String(record.sector) === String(sector) &&
                    String(record.manzana) === String(manzana)
                ).then(resolve).catch(reject);
            }
        });
    }

    /**
     * Get database version
     * @returns {number} - Current database version
     */
    getVersion() {
        return this.version;
    }

    /**
     * Check if database is initialized
     * @returns {boolean} - Initialization status
     */
    isReady() {
        return this.isInitialized && !!this.db;
    }
}

 
 
// ============================================================================
// Set up callback function BEFORE initializing database
// ============================================================================

// Define what happens when database is ready
window.onLocalDBReady = function(dbInstance) {
    console.log("🎯 Database ready callback triggered!");
    console.log("📊 Database instance:", dbInstance);
    
    // Now you can safely use the database
    // Add any initialization logic here that needs the database
    console.log("✅ EasyDB is fully ready for operations");
};

// ============================================================================
// Initialize EasyDB with callback support
// ============================================================================

var localDB = null;
const stores = ["usuarios", "areas", "horarios", "attendance", "info"];
localDB = new EasyDB("dbCobaed", stores);

// Initialize and the callback will be triggered automatically
window.localDBInitialized = localDB.init()
    .then(() => {
        console.log("✅ EasyDB initialized successfully");
        
        // Call custom ready callback if available (this will call our function above)
        if (typeof window.onLocalDBReady === "function") {
            window.onLocalDBReady(localDB);
        }
        
        return localDB;
    })
    .catch(error => {
        console.error("❌ Failed to initialize EasyDB:", error);
        throw error;
    });
    